"""Calculation"""

import numpy as np
import pandas as pd
from tabulate import tabulate
import os
from eto import ETo
import requests
from dotenv import load_dotenv
import json
import datetime

load_dotenv()

WEATHER_API_KEY = os.getenv("WEATHER_API_KEY")

def calculation(json_data, prev_day_data = None, corresponding_crop = None, corresponding_soil = None, weather_data = None):

    if json_data:
        df = pd.DataFrame(json_data)
        crop = df[['crop']].transpose()
        soil = df[['soil']].dropna().transpose()
        stages = crop['stages'].values
    else:
        crop = pd.DataFrame([corresponding_crop])
        soil = pd.DataFrame([corresponding_soil])
        stages = crop['stages'].values[0]

    if prev_day_data is not None:
        user_id = prev_day_data['owner_id']
        crop_id = corresponding_crop['id']
        soil_id = corresponding_soil['id']
        soil_depth = int(corresponding_soil['depth'])
    else:
        user_id = df[['user']].transpose()['id'].values[0]
        crop_id = df[['crop']].transpose()['id'].values[0]
        soil_id = df[['soil']].transpose()['id'].values[0]
        soil_depth = float(df[['soil']].transpose()['depth'].values[0])

    """Using weather api's data"""

    if weather_data is not None:
        weather = weather_data
    else:
        url = f"https://api.tomorrow.io/v4/weather/forecast?location=oujda&timesteps=daily&units=metric&apikey={WEATHER_API_KEY}"
        headers = {"accept": "application/json"}
        response = requests.get(url, headers=headers)

        weather = pd.DataFrame(response.json())

    """Working w/ Temporary data stored in json file for testing purposes."""

    # data_folder = os.path.join(os.getcwd(), 'data')
    # weather_file = os.path.join(data_folder, 'data.json')

    # weather = pd.read_json(weather_file)

    """Retrieve necessary data from the weather API"""

    data = weather.loc['daily']['timelines']

    #date-time

    date = data[0]['time']

    #et0

    et0 = data[0]['values']['evapotranspirationAvg']
    # et0_max = data[0]['values']['evapotranspirationMax']
    if json_data:
        et0_max = et0
    else:
        et0_max = prev_day_data['et0_max']

    #precipitations

    P = data[0]['values']['rainAccumulationSum']

    if prev_day_data is not None:
        das = prev_day_data['das'] + 1
    else:
        das = 1

    if json_data:
        len_stages = [stages[0]['A']['length'], stages[0]['B']['length'], stages[0]['C']['length'], stages[0]['D']['length']]
    else:
        stages = json.loads(crop['stages'].values[0])
        len_stages = [stages['A']['length'], stages['B']['length'], stages['C']['length'], stages['D']['length']]


    # check if irrigation cycle has finished:

    if prev_day_data is not None:
        if prev_day_data['das'] > sum(len_stages):
            return {"message" : "irrigation cycle ended."}
    
    # Kc calculation
    Kc = 0

    Kc = kc_calculation(len_stages, das, stages, json_data)
        
    # ETc calculation 
    ETc = Kc * et0

    # theorical Zroot Calculation
    Zroot = zroot_calculation(crop, das, len_stages, json_data)
    
    # practical Zroot value (taking the min value between 1 and Zroot)
    Zroot_real = soil_depth if Zroot > soil_depth else Zroot

    # critical soil-water deficit calculation : threshold indicating the right moment of calculation
    SWDc = swdc_calculation(crop, soil, et0_max, Zroot_real)

    # actual soil-water deficit amount
    """ if request didn't provide data of (day - 1) then initialize SWD with 0 otherwise use its value coming from request data. """
    SWD = 0 if prev_day_data == None else prev_day_data["swd"] # or the value from (day - 1)

    # irrigation amount
    """ if request didn't provide data of (day - 1) then initialize I with 0 otherwise use its value coming from request data. """
    I = 0 if prev_day_data == None else prev_day_data["irr"] # 0 or the value from (day - 1)

    SWD = SWD + ETc - (P + I) # water balance equation

    I = SWDc if (SWD + ETc - P) > SWDc else 0

    return {
        "et0" : float(et0), 
        "precipitations" : float(P), 
        "kc" : Kc, 
        "etc" : ETc, 
        "srad" : None, 
        "wind_speed" : float(data[0]['values']['windSpeedAvg']), 
        "r_hmin" : float(data[0]['values']['humidityMin']), 
        "r_hmax": float(data[0]['values']['humidityMax']), 
        "t_min" : float(data[0]['values']['temperatureMin']), 
        "t_max" : float(data[0]['values']['temperatureMax']),
        "swd" : float(SWD), 
        "swdc" : float(SWDc), 
        "irr" : float(I), 
        "das": das,
        "z_root_real" : float(Zroot_real), 
        "z_root" : float(Zroot), 
        "owner_id" : int(user_id), 
        "crop_id" : int(crop_id),
        "soil_id": int(soil_id)
    }

# UTILS:

def swdc_calculation(crop_data, soil_data, et0_max, real_zroot):
    fad = float(crop_data['fad'].values[0])

    paw = float(soil_data['paw'].values[0])
    AD = 0.67 if fad <= 0 else float(1 - et0_max * fad)
    
    return real_zroot * paw * AD

def zroot_calculation(crop_data, das, len_stages, json_data):

    if json_data:
        sow_depth = float(crop_data['sowDepth'].values[0])
        max_root_depth = float(crop_data['maxRootDepth'].values[0])
    else:
        sow_depth = float(crop_data['sow_depth'].values[0])
        max_root_depth = float(crop_data['max_root_depth'].values[0])
    
    duration = len_stages[0] + len_stages[1]

    return max_root_depth if das > duration else sow_depth + ( max_root_depth - sow_depth ) / duration * das

def kc_calculation(len_stages, das, stages, json_data):

    if das <= len_stages[0]:
        
        KcMin = stages[0]['A']['KcMin'] if json_data else stages['A']['KcMin']
        KcMax = stages[0]['A']['KcMax'] if json_data else stages['A']['KcMax']
        len_phase = stages[0]['A']['length'] if json_data else stages['A']['length']

        Kc = KcMin + ( KcMax - KcMin ) / len_phase * (das)

    elif das > len_stages[0] and das <= len_stages[1]:

        KcMin = stages[0]['B']['KcMin'] if json_data else stages['B']['KcMin']
        KcMax = stages[0]['B']['KcMax'] if json_data else stages['B']['KcMax']
        len_phase = stages[0]['B']['length'] if json_data else stages['B']['length']

        Kc = KcMin + ( KcMax - KcMin ) / len_phase * (das - len_stages[0])

    elif das > len_stages[1] and das <= len_stages[2]:

        KcMin = stages[0]['C']['KcMin'] if json_data else stages['C']['KcMin']
        KcMax = stages[0]['C']['KcMax'] if json_data else stages['C']['KcMax']
        len_phase = stages[0]['C']['length'] if json_data else stages['C']['length']

        Kc = KcMin + ( KcMax - KcMin ) / len_phase * (das - len_stages[1])

    else:

        KcMin = stages[0]['D']['KcMin'] if json_data else stages['D']['KcMin']
        KcMax = stages[0]['D']['KcMax'] if json_data else stages['D']['KcMax']
        len_phase = stages[0]['D']['length'] if json_data else stages['D']['length']

        Kc = KcMin + ( KcMax - KcMin ) / len_phase * (das - len_stages[2])

    return Kc