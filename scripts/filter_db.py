#!/usr/bin/env python3
import csv
import argparse
from argparse import RawTextHelpFormatter
import sys

#Extremely long fields break the csv reader obj so need to do this.
csv.field_size_limit(sys.maxsize)

parser = argparse.ArgumentParser(description="A script to filter the output from make_db.py a different " +
                    "user set thresholds described below. None are required by default", formatter_class=RawTextHelpFormatter)
parser.add_argument("--percent", help="The percentage identity between query and subject. " +
                    "filters out all values less than this number"+"\n\t"+"[default=0]\n\n")
parser.add_argument("--retro", help="Add this to the command line if you only want to see \n"
                                    "retro viruses. Leave it out if you don't want to see them.\n\n", action="store_true")
parser.add_argument("--subdiv", help="Exclude these divisions in the new file.\n[default=show all]")
parser.add_argument("--input", help="Input file", required=True)
parser.add_argument("--output", help="Output file", required=True)
args = parser.parse_args()


#'''
# Command line arguments
#'''

#'''
# If percent is not given to the command line make it equal to 0, if it is make it a float.
#'''
if args.percent == None:
    percent = 0
else:
    percent = float(args.percent)

#'''
# If retro is not given to the command line make it a list of both yes and no, if it is, make sure each element in list is upper
# this code still works even if the user gives one string, because splitting a string makes a list.
#'''
if args.retro == False:
    retro = "NO"
else:
    retro = "YES"

#'''
# If no subject division is given make it a list of all possible subject division. If it is, split each element of users list
# and make it upper
#'''
if args.subdiv == None:
    subject_division = []
else:
    subject_division = args.subdiv.split(",")
    subject_division = [i.upper() for i in subject_division]

#'''
# Open outfile and print a header line
#'''
out_file = open(args.output, "w")

print("Query\tSubject\tPercentage\tAlignment_length\tMismatch\tGap_Open\tQuery_start\tQuery_end\tSubject_start\tSubject_end\tEvalue\tBitscore\tQuery_length\tSubject_length\tRetro\tQuery_accession"+
"\tQuery_taxid\tQuery_division\tQuery_html\tSubject_accession\tSubject_taxid\tSubject_division\tSubject_html",file=out_file)

#'''
# Start writing to the output file all lines in file which meet the criteria from command line.
#'''
with open(args.input) as f:
    data = csv.reader(f, delimiter="\t")
    next(data, None)
    for line in data:
        if float(line[2]) >= percent and line[21] not in subject_division and line[14] == retro:
            print("\t".join(line),file=out_file)
