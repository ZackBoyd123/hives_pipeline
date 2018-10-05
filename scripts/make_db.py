#!/usr/local/bin/python3
import csv
import argparse
import sys 

#Extremely long fields break the csv reader obj so need to do this.
csv.field_size_limit(sys.maxsize)

parser = argparse.ArgumentParser(description= "A script which melts the blast outfmt 6 format into a " +
                    "format ready to be loaded into SQL. This was written specifically " +
                    "for blasting sequences against viral sequnces, so the headers are tailored " +
                    "toward that")
parser.add_argument("--input", required=True, help="Input file: this must be produced from blast with the" +
                    "--outfmt 6 option specified.")
parser.add_argument("--output", required=True, help="What you want your output file to be called. ")
parser.add_argument("--retro", required=True, help="A file containing one column of all retrovirus taxids.")
args = parser.parse_args()

out_file = open(args.output,"w")

print("Query\tSubject\tPercentage\tAlignment_length\tMismatch\tGap_Open\tQuery_start\tQuery_end\tSubject_start\tSubject_end\tEvalue\tBitscore\tQuery_length\tSubject_length\tRetro\tQuery_accession"+
"\tQuery_taxid\tQuery_division\tQuery_html\tSubject_accession\tSubject_taxid\tSubject_division\tSubject_html",file=out_file)

retro_file = open(args.retro)
retro_list = retro_file.readlines()
retro_list = [i.replace("\n","") for i in retro_list]

html_stub = "https://www.ncbi.nlm.nih.gov/nuccore/"
with open(args.input) as f:
    data = csv.reader(f, delimiter="\t")
    for line in data:

        query_acc = "".join(line[0].split("|")[3])
        query_taxid = "".join(line[0].split("|")[4])
        query_div = "".join(line[0].split("|")[5])

        subject_acc = "".join(line[1].split("|")[3])
        subject_taxid = "".join(line[1].split("|")[4])
        subject_div = "".join(line[1].split("|")[5])

        if subject_taxid in retro_list or query_taxid in retro_list:
            is_retro = "YES"
        else:
            is_retro = "NO"

        print("\t".join(line)+"\t"+is_retro+"\t"+query_acc+
              "\t"+query_taxid+"\t"+query_div+"\t"+html_stub+query_acc+"\t"+
              subject_acc+"\t"+subject_taxid+"\t"+subject_div+"\t"+html_stub+subject_acc,file=out_file)
