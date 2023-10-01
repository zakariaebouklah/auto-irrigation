"""Access MySQL Database."""

import mysql.connector
from fastapi.exceptions import HTTPException
import calculation_script
import requests
from dotenv import load_dotenv
import os
import pandas as pd
import datetime

load_dotenv()

WEATHER_API_KEY = os.getenv("WEATHER_API_KEY")

connexion = mysql.connector.connect(
    host="mysql",
    user="root",
    password="root",
    database="irrigation_db"
)

"""Read latests outputs from database"""

def read_latests_outputs():

    try:

        if not connexion.is_connected():
            connexion.reconnect(attempts=3, delay=0)

        """ the following query is used to retrieve the last "output" for each farmer(user) 
            who has already started an irrigation control/follow-up of one or many crops
            and retrieves also the crop_id and soil_id to follow calculation on them...
            so basically i'm retrieving the last output of each crop that belong to each farmer(user)
        """
        
        query = """
            SELECT o.owner_id, o.id as output_id, o.das, o.swd, o.irr, et0_max, c.id as crop_id, c.crop_name, s.id as soil_id, s.type as soil_type FROM output o 
            INNER JOIN 
            ( SELECT MAX(et0) as et0_max, MAX(id) as last_output_id FROM output GROUP BY owner_id, crop_id, soil_id ) o2 
            ON o.id = o2.last_output_id 
            INNER JOIN crop c ON o.crop_id = c.id 
            INNER JOIN soil s ON o.soil_id = s.id
        """

        cursor = connexion.cursor()
        cursor.execute(query)

        records = []

        results = cursor.fetchall()

        if results:
            columns = [col[0] for col in cursor.description]
            for result in results:
                row  = {columns[i] : result[i] for i in range(len(columns))}
                records.append(row)
            return records
        else:
            return {"msg" : "failure."}

    except mysql.connector.Error as error:
        raise HTTPException(status_code=400, detail=str(error))
    finally:
        cursor.close()
        connexion.close()
    
"""Read the corresponding crop"""

def get_crop(id):

    try:
        
        if not connexion.is_connected():
            connexion.reconnect(attempts=3, delay=0)
        
        query = f"SELECT * FROM crop WHERE id = {id}"
        cursor = connexion.cursor()

        cursor.execute(query)

        result = cursor.fetchone()

        if result:
            columns = [col[0] for col in cursor.description]
            crop_data = {columns[i]: result[i] for i in range(len(columns))}

        return crop_data

    except mysql.connector.Error as error:
        raise HTTPException(status_code=400, detail=str(error))
    finally:
        cursor.close()
        connexion.close()

"""Read the corresponding soil"""

def get_soil(id):

    try:
        
        if not connexion.is_connected():
            connexion.reconnect(attempts=3, delay=0)
        
        query = f"SELECT * FROM soil WHERE id = {id}"

        cursor = connexion.cursor()
        cursor.execute(query)

        result = cursor.fetchone()

        if result:
            columns = [col[0] for col in cursor.description]
            soil_data = {columns[i]: result[i] for i in range(len(columns))}

        return soil_data

    except mysql.connector.Error as error:
        raise HTTPException(status_code=400, detail=str(error))
    finally:
        cursor.close()
        connexion.close()

"""Store output(s) in database"""

def store_outputs(output: dict):

    try:

        if not connexion.is_connected():
            connexion.reconnect(attempts=3, delay=0)

        query = """
                INSERT INTO output 
                (et0, das, precipitations, kc, etc, s_rad, wind_speed, r_hmin, r_hmax, t_min, t_max, 
                date_of_calculations, swd, swdc, irr, z_root_real, z_root, owner_id, crop_id, soil_id) 
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """
        values = (
            output.get('et0'),
            output.get('das'),
            output.get('precipitations'),
            output.get('kc'),
            output.get('etc'),
            output.get('s_rad'),
            output.get('wind_speed'),
            output.get('r_hmin'),
            output.get('r_hmax'),
            output.get('t_min'),
            output.get('t_max'),
            datetime.datetime.now(),
            output.get('swd'),
            output.get('swdc'),
            output.get('irr'),
            output.get('z_root_real'),
            output.get('z_root'),
            output.get('owner_id'),
            output.get('crop_id'),
            output.get('soil_id')
        )

        cursor = connexion.cursor()
        cursor.execute(query, values)
        connexion.commit()
        return {"message": "Output created successfully."}
    except mysql.connector.Error as error:
        connexion.rollback()
        raise HTTPException(status_code=400, detail=str(error))
    finally:
        cursor.close()

"""Requiring calculations for each prev output"""

def require_calculations():

    """
        Making single call to Weather API and pass its data to the calculation function 
        instead of making request for each iteration in the loop below
    """
    url = f"https://api.tomorrow.io/v4/weather/forecast?location=oujda&timesteps=daily&units=metric&apikey={WEATHER_API_KEY}"
    headers = {"accept" : "application/json"}
    response = requests.get(url, headers=headers)

    weather = pd.DataFrame(response.json())

    outputs = read_latests_outputs()

    for output in outputs:

        new_output = calculation_script.calculation(None, output, get_crop(output['crop_id']), get_soil(output['soil_id']), weather_data=weather)
        store_outputs(new_output)


require_calculations()