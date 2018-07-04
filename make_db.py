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
parser.add_argument("--retro", required=True, help="A file containing one column of all retrovirus taxids.")
args = parser.parse_args()

out_file = open(args.output,"w")

print("Query\t"+"Subject\t"+"Percentage\t"+"Alignment_length\t"+"Query_start\t"+"Query_end\t"+"Query_len\t"
      +"Subject_start\t"+"Subject_end\t"+"Subject_len\t"+"Evalue\t"+"Bitscore\t"+"Retrovirus\t"+
      "Query_accession\t"+"Query_taxid\t"+"Query_div\t"+"Query_html\t"+"Subject_accession\t"+"Subject_taxid\t"+
      "Subject_div\t"+"Subject_html",file=out_file)

retro_file = open(args.retro)
retro_list = retro_file.readlines()
retro_list = [i.replace("\n","") for i in retro_list]

html_stub = "https://www.ncbi.nlm.nih.gov/nuccore/"
with open(args.input) as f:
    data = csv.reader(f, delimiter="\t")
    for line in data:

        query_acc = "".join(line[0].split("|")[3])
        query_taxid = "".join(line[0].rsplit("|")[-3])
        query_div = "".join(line[0].rsplit("|")[-2])
        subject_acc = "".join(line[1].split("|")[3])
        subject_taxid = "".join(line[1].rsplit("|")[-3])
        subject_div = "".join(line[1].rsplit("|")[-2])

        if query_taxid in retro_list:
            is_retro = "YES"
        else:
            is_retro = "NO"

        print("\t".join(line)+"\t"+is_retro+"\t"+query_acc+
              "\t"+query_taxid+"\t"+query_div+"\t"+html_stub+query_acc+"\t"+
              subject_acc+"\t"+subject_taxid+"\t"+subject_div+"\t"+html_stub+subject_acc,file=out_file)
