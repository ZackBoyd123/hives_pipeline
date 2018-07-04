#!/usr/local/bin/python3 
import csv
import argparse

parser = argparse.ArgumentParser(description= "A script which melts the blast outfmt 6 format into a " +
                    "format ready to be loaded into SQL. This was written specifically " + 
                    "for blasting sequences against viral sequnces, so the headers are tailored " + 
                    "toward that")
parser.add_argument("--input", required=True, help="Input file: this must be produced from blast with the" +
                    "--outfmt 6 option specified.")
parser.add_argument("--output", required=True, help="What you want your output file to be called. ")
args = parser.parse_args()


#'''
# To do add in whether or not query is a retro virus.
#'''
out_file = open(args.output,"w")

print("Query\t"+"Subject\t"+"Percentage\t"+"Alignment_length\t"+"Mismatches\t"+"Gap_openings\t"+
      "Query_start\t"+"Query_end\t"+"Subject_start\t"+"Subject_end\t"+"Evalue\t"+"Bitscore\t"+
      "Query_accession\t"+"Query_taxid\t"+"Query_html\t"+"Subject_accession\t"+"Subject_taxid\t"+
      "Subject_html",file=out_file)

html_stub = "https://www.ncbi.nlm.nih.gov/nuccore/"
with open(args.input) as f:
    data = csv.reader(f, delimiter="\t")
    for line in data:

        query_acc = "".join(line[0].split("|")[3])
        query_taxid = "".join(line[0].rsplit("|")[-3])
        subject_acc = "".join(line[1].split("|")[3])
        subject_taxid = "".join(line[1].rsplit("|")[-3])

        print("\t".join(line)+"\t"+query_acc+
              "\t"+query_taxid+"\t"+html_stub+query_acc+"\t"+
              subject_acc+"\t"+subject_taxid+"\t"+html_stub+subject_acc,file=out_file)
