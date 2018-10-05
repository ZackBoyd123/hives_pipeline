#!/usr/local/bin/python3
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
# Open both files, pull out the taxid and division into a dict.
#   { TAXID : DIV }
#
# Check each line in the accession file (third col) to see if it is in the dict.  
# If it is, print it to file.
#'''

dump_dict = {}
with open(accession_in) as a, open(dump_in) as d:
    a_delim = csv.reader(a, delimiter="\t")
    d_delim = csv.reader(d, delimiter="\t")
    next(a_delim, None)
    print("Reading nodes.dmp")
    for line in d_delim:
        dump_dict[line[0]] = line[8]
    print("DONE!!")
    print("Pairing, this may take a while...")
    sys.stdout = open("converted_accession_file.txt", "w")
    for line in a_delim:
        if line[2] in dump_dict:
            if dump_dict[line[2]] == "0":
                print(line[1]+"\t"+line[2]+"\t"+"BACTERIA")
            elif dump_dict[line[2]] == "1":
                print(line[1]+"\t"+line[2]+"\t"+"INVERTEBRATE")
            elif dump_dict[line[2]] == "2":
                print(line[1]+"\t"+line[2]+"\t"+"MAMMAL")
            elif dump_dict[line[2]] == "3":
                print(line[1]+"\t"+line[2]+"\t"+"PHAGE")
            elif dump_dict[line[2]] == "4":
                print(line[1]+"\t"+line[2]+"\t"+"PLANTS_&_FUNGI")
            elif dump_dict[line[2]] == "5":
                print(line[1]+"\t"+line[2]+"\t"+"PRIMATES")
            elif dump_dict[line[2]] == "6":
                print(line[1]+"\t"+line[2]+"\t"+"RODENT")
            elif dump_dict[line[2]] == "7":
                print(line[1]+"\t"+line[2]+"\t"+"SYNTHETIC_&_CHIMERIC")
            elif dump_dict[line[2]] == "8":
                print(line[1]+"\t"+line[2]+"\t"+"UNASSIGNED")
            elif dump_dict[line[2]] == "9":
                print(line[1]+"\t"+line[2]+"\t"+"VIRUS")
            elif dump_dict[line[2]] == "10":
                print(line[1]+"\t"+line[2]+"\t"+"VERTEBRATE")
            elif dump_dict[line[2]] == "11":
                print(line[1]+"\t"+line[2]+"\t"+"ENVIRONMENTAL")
print("DONE!!")
