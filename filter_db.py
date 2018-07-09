#!/usr/local/bin/python3
import csv
import argparse
from argparse import RawTextHelpFormatter

parser = argparse.ArgumentParser(description="A script to filter the output from make_db.py a different " +
                    "user set thresholds described below. None are required by default", formatter_class=RawTextHelpFormatter)
parser.add_argument("--percent", help="The percentage identity between query and subject. " +
                    "filters out all values less than this number"+"\n\t"+"[default=0]\n\n")
parser.add_argument("--retro", help="Specify this option with 'yes' if you want to see only retroviruses" +
                    " specifiy with 'no' if you only want to see phages, don't give this flag to command line" +
                    " if you want to see both.\n\n")
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
if args.retro == None:
    retro = ["YES", "NO"]
else:
    retro = args.retro.split(",")
    retro = [i.upper()for i in retro]

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
print("Query\t"+"Subject\t"+"Percentage\t"+"Alignment_length\t"+"Query_start\t"+"Query_end\t"+"Query_len\t"
      +"Subject_start\t"+"Subject_end\t"+"Subject_len\t"+"Evalue\t"+"Bitscore\t"+"Retrovirus\t"+
      "Query_accession\t"+"Query_taxid\t"+"Query_div\t"+"Query_html\t"+"Subject_accession\t"+"Subject_taxid\t"+
      "Subject_div\t"+"Subject_html",file=out_file)

#'''
# Start writing to the output file all lines in file which meet the criteria from command line.
#'''
with open(args.input) as f:
    data = csv.reader(f, delimiter="\t")
    next(data, None)
    for line in data:
        if float(line[2]) >= percent and line[-2] not in subject_division and line[12] in retro:
            print("\t".join(line),file=out_file)
