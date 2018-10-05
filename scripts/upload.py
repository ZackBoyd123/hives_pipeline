#!/usr/bin/env python3
import pymysql as mysql
import glob
import argparse
parser = argparse.ArgumentParser()
parser.add_argument('--user', help='MySQL username', required=True)
parser.add_argument('--password', help='MySQL password', required=True)
args = parser.parse_args()

conn = mysql.connect("localhost", str(args.user), str(args.password), "virus", local_infile=True)
cursor = conn.cursor()

file_list = []
for file in glob.glob('*.txt'):
    filename = file.split("_")[0]
    file_list.append(filename)

for i in set(file_list):
    drop_query = "DROP TABLE IF EXISTS {tab_name}"
    best_drop = drop_query.format(tab_name=str(i) + '_best')
    cursor.execute(best_drop)
    worst_drop = drop_query.format(tab_name=str(i) + '_worst')
    cursor.execute(worst_drop)

    create_query = '''
CREATE TABLE {tab_name} 
( `Query` VARCHAR(100) NOT NULL , `Subject` VARCHAR(100) NOT NULL , `Percentage` FLOAT(8) NOT NULL , 
`Alignment_Length` INT(12) NOT NULL , `Mismatch` INT(8) NOT NULL , `Gap_Open` INT(8) NOT NULL ,
`Query_Start` INT(12) NOT NULL , `Query_End` INT(12) NOT NULL , `Subject_Start` INT(12) NOT NULL , 
`Subject_End` INT(12) NOT NULL , `EValue` VARCHAR(10) NOT NULL , `Bitscore` INT(8) NOT NULL ,
`Query_Length` INT(12) NOT NULL , `Subject_Length` INT(12) NOT NULL , `Retro` VARCHAR(5) NOT NULL ,
`Query_Accession` VARCHAR(50) NOT NULL , `Query_Taxid` INT(12) NOT NULL ,
`Query_Division` VARCHAR(20) NOT NULL , `Query_HTML` VARCHAR(500) NOT NULL ,
`Subject_Accession` VARCHAR(50) NOT NULL , `Subject_Taxid` INT(12) NOT NULL ,
`Subject_Division` VARCHAR(20) NOT NULL ,
`Subject_HTML` VARCHAR(500) NOT NULL,
KEY `Query_Taxid`(`Query_Taxid`),
KEY `Subject_Taxid`(`Subject_Taxid`)
 ) ENGINE = InnoDB;
'''
    best_query = create_query.format(tab_name=str(i) + '_best')
    cursor.execute(best_query)
    worst_query = create_query.format(tab_name=str(i) + '_worst')
    cursor.execute(worst_query)




for file in glob.glob('*.txt'):
    filename = file.split('_')[0]

    load_query = '''
    LOAD DATA LOCAL INFILE '{filen}' INTO TABLE `{tab_name}` 
    FIELDS TERMINATED BY '\\t'
    LINES TERMINATED BY '\\n'
    IGNORE 1 LINES
    '''
    if 'best' in file:
        load_query = load_query.format(filen=str(file), tab_name=str(filename) + '_best')
    else:
        load_query = load_query.format(filen=str(file), tab_name=str(filename) + '_worst')

    cleanse_rows = '''
    DELETE FROM `{tab_name}` WHERE Percentage <= 94
    '''
    if 'best' in file:
        cleanse_rows = cleanse_rows.format(filen=str(file), tab_name=str(filename) + '_best')
    else:
        cleanse_rows = cleanse_rows.format(filen=str(file), tab_name=str(filename) + '_worst')
    #print(load_query)
    cursor.execute(load_query)
    cursor.execute(cleanse_rows)

conn.commit()
conn.close()
