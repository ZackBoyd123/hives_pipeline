#!/usr/bin/python3
import sys
import csv
import argparse
from datetime import datetime
parser = argparse.ArgumentParser(description= "A script which generates a a list of accession numbers "+
            "paired with tax ids and their corresponding taxonomy based on three input files.")
parser.add_argument("--accession", help= "The accession2taxid file from the ncbi webstite.", 
                    required=True)
parser.add_argument("--nodes", help= "The nodes.dmp file from the ncbi website", 
                    required= True)
args = parser.parse_args()

accession_in = args.accession
dump_in = args.nodes
start_time = datetime.now()
print("Starting script at:\t"+str(start_time))

#'''
#Open the accession number file and pull out the accession version and tax id, then store this as a dictionary.
#As the accession is always unique this will work. Data is stored like:
#    { ACCNUM : TAXID }
#'''
access_dict = {}
count = 0
read = 0
print("Reading accession numbers.....")
with open(accession_in) as file:
    data = csv.reader(file, delimiter="\t")
    next(data, None)
    for line in data:
        count += 1
        access_dict[line[1]] = line[2]
        if count % 2000000 == 0:
            print("Read:\t"+str(int(read+2))+"m\tsequences")
            read = read + 2
print("DONE!!")

#'''
# Grab all the unique tax id from the dump file, and the corresponding division number and store this as
# a dictionary. Data is in the format:
#    { TAXID : DIVNUM }
#'''
print("Reading nodes.....")
dump_dict = {}
with open(dump_in) as dump:
    dump_delim = csv.reader(dump, delimiter="\t")
    for line in dump_delim:
        dump_dict[line[0]] = line[8]
print("DONE!!")

#'''
# For each element in the new dictionary, check if the tax id in the the dump file dictionary. If it is print
# all of the information required. I have changed the division ids to the actual taxonomy.
#'''
sys.stdout = open("converted_accession_file.txt","w")
print("ACCESSION NUMBER\t"+"TAX ID\t"+"DIVISON")
for i in access_dict:
    if access_dict[i] in dump_dict:
        if dump_dict[access_dict[i]] == "0":
            print(i+"\t"+access_dict[i]+"\t"+"BACTERIA")
        elif dump_dict[access_dict[i]] == "1":
            print(i+"\t"+access_dict[i]+"\t"+"INVERTEBRATE")
        elif dump_dict[access_dict[i]] == "2":
            print(i+"\t"+access_dict[i]+"\t"+"MAMMAL")
        elif dump_dict[access_dict[i]] == "3":
            print(i+"\t"+access_dict[i]+"\t"+"PHAGE")
        elif dump_dict[access_dict[i]] == "4":
            print(i+"\t"+access_dict[i]+"\t"+"PLANTS & FUNGI")
        elif dump_dict[access_dict[i]] == "5":
            print(i+"\t"+access_dict[i]+"\t"+"PRIMATES")
        elif dump_dict[access_dict[i]] == "6":
            print(i+"\t"+access_dict[i]+"\t"+"RODENT")
        elif dump_dict[access_dict[i]] == "7":
            print(i+"\t"+access_dict[i]+"\t"+"SYNTHETIC & CHIMERIC")
        elif dump_dict[access_dict[i]] == "8":
            print(i+"\t"+access_dict[i]+"\t"+"UNASSIGNED")
        elif dump_dict[access_dict[i]] == "9":
            print(i+"\t"+access_dict[i]+"\t"+"VIRUS")
        elif dump_dict[access_dict[i]] == "10":
            print(i+"\t"+access_dict[i]+"\t"+"VERTEBRATE")
        elif dump_dict[access_dict[i]] == "11":
            print(i+"\t"+access_dict[i]+"\t"+"ENVIRONMENTAL")


