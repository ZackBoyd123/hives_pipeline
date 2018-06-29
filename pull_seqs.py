#!/usr/bin/python3
import csv
import argparse
from datetime import datetime

parser = argparse.ArgumentParser(description= "Split a fasta file into sequences"+
                        "based on ids in a seperate input file. This is part of a blast pipeline")
parser.add_argument("--fasta", help= "An input fasta file.", required=True)
parser.add_argument("--accessions", help= "An input file with accession numbers in the first column",
                    required=True)
parser.add_argument("--output", help= "What to call your output file", required=True)
args = parser.parse_args()

#'''
# Input args from the command line. 
# Also a var is initiatied with the current time, to get how long the script runs for. 
#'''
file_in = args.fasta
seq_ids = args.accessions
out_file = args.output
start_time = datetime.now()


#'''
# Open up the converted accession file and pull the ids out into a dictionary.
# print a progress counter to screen when 1m tax ids are read.
# The dictionary is in the format:
#   { ID : DESCRIPTION }
#   { HQ200558.1 : |TAXID|DIVISION| }
#'''
print("Getting tax ids from input file....")
ids = {}
count = 0
read = 0
with open(seq_ids) as seq_file:
    data = csv.reader(seq_file, delimiter="\t")
    for line in data:
        val = "|".join(line[1:3])+"|"
        key = line[0]
        ids[key] = val
        count += 1
        if count % 2000000 == 0:
            print("Read:\t"+str(int(read + 2))+"m.\ttax ids.")
            read = read + 2
    seq_file.close()

print("DONE!\nTotal tax ids in file:\t"+str(count))

#'''
# Open the fasta file and process line by line.
# Look at the header lines, and if the accession number is in them and the ids dictionary set a bool to True.
# Whilst the bool is true, print all lines (including seq) to outfile.
# This bool will change to False everytime a header is read which doesnt contain an accession
# number in the ids dictionary.
#'''
print("Beginning to pull sequences from the fasta file and write to:\t"+str(args.output))
new_count = 0
read = 0
out_file = open(out_file,"w")
with open(file_in) as file:
    for line in file:
        if line.startswith(">"):
            if line.split("|")[3] in ids:
                test = True
            else:
                test = False
        if test:
            if line.startswith(">"):
                new_count += 1
                start = "|".join(line.split("|")[0:4])
                accession = line.split("|")[3]
                end = "|".join(line.split("|")[4:])
                line = start+"|"+ids[line.split("|")[3]]+end
        
                # Progress statement 
                if new_count % 2000000 == 0:
                    print("Processed:\t"+str(int(read + 2))+".\tSequences.")
                    read = read + 2

            print(line,end="",file=out_file)

print("\nTotal sequences processed:\t"+str(new_count))
print("\nThat took:\t"+str(datetime.now()-start_time))
