import json
import pymysql

host = "localhost"
user = "root"
pwd = ""
db_name = "HWI"

db = pymysql.connect(host, user, pwd, db_name)
cur = db.cursor()

with open('drug.json') as f:
  data = json.load(f)
#print(len(data['results']))

c = 0
for drug in data['results']:
    try:
        cur.execute('select * from medicine where name = "{0}"'.format(str(drug['patient']['drug'][0]['openfda']['generic_name'][0]).lower().capitalize()))
        res = cur.fetchall()
        if len(res) >=1:
            continue
        cur.execute( 'Insert into medicine(Name,Quantity,Type) values("{0}",{1},"{2}")'.format(
              str(drug['patient']['drug'][0]['openfda']['generic_name'][0]).lower().capitalize(), 10,
            str(drug['patient']['drug'][0]['openfda']['product_type'][0]).lower().capitalize()
              ));
        db.commit()
        c+=1
    except Exception as e:
        print(e)
   
    if c == 100:
        break
    
