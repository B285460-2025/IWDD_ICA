import subprocess
import json
import sys
from datetime import datetime as dt
import os.path
import re

def return_output(data, status):
    print(json.dumps({
        'data': data,
        'status': status}))
    exit()

# Now - used to give unique names to temp files
now = dt.now().strftime('%d_%m_%y_%H_%M_%S')
filename = sys.argv[1]

# Read the input data and convert it into something useable in python
try:
    with open(filename, "r") as infile:
        input_data = infile.read().strip()
        input_data = json.loads(input_data)
except Exception as err:
    return_output(f"Failed to load input data > {err}", 1)

infile = f"temp/pepstat_{now}.fasta"
outfile = f"temp/pepstat_{now}.txt"

# Write all sequences to a temp file in fasta format
try:
    for ncbi_id, record in input_data.items():
        seq = f">{record['ncbi_id']}\n{record['sequence']}\n"

        with open(infile, 'a') as file:
            file.write(seq)
except Exception as err:
    return_output(f"Failed to write sequence data to temp file > {err}", 1)

# Run pepstats
try:
    subprocess.call(f"pepstats -sequence {infile} -outfile {outfile}", shell=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    # Check output file has been created
    if not os.path.isfile(outfile):
        return_output(f"Oops, there was an issue running pepstats > {err}", 1)
except Exception as err:
    return_output(f"Oops, there was an issue running pepstats > {err}", 1)

output_dict = {}
# parse output to split up and pass back individual reports
with open(outfile, 'r') as results:
    pepstats_out = results.read().strip().rstrip()
    pepstats_out = pepstats_out.split('PEPSTATS of')[1:]
    for item in pepstats_out:
        id = item.split('from')[0].strip().rstrip()
        item = 'PEPSTATS of' + item
        output_dict[id] = item

try:
    # Cleanup the temp files
    subprocess.call(f"rm {infile} {outfile}", shell=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
except Exception as err:
    return_output(f"Failed to remove temporary pepstats files > {err}", 1)

return_output(output_dict, 0)
