from Bio import Entrez, SeqIO
import subprocess
import sys
import json
from base64 import b64decode


# Use this when we want to exit so we can display error messages
def return_output(data, status):
    print(json.dumps({
        'data': data,
        'status': status}))
    exit()


# api key is a system related variable, I've set this in my .bashrc file. You'll need to set this yourself or change the value if running elsewhere
Entrez.email = "s2891770@ed.ac.uk"
Entrez.api_key = subprocess.check_output("echo ${NCBI_API_KEY}", shell=True).rstrip().decode('utf-8')

# Switching between local development and development on the msc8 server. Locally I have api key set to env_var, can't seem to get this working the same way on msc8 so having to resort to reading from file
if not Entrez.api_key:
    with open('/localdisk/home/s2891770/.secrets/ncbi_api_key', 'r') as key:
        Entrez.api_key = key.read().strip()


# Decode the encoded input value to get the original list of IDs
ncbi_ids = json.loads(b64decode(sys.argv[1]).decode())

fasta_dict = {}
for ncbi_id in ncbi_ids:
    try:
        efetch_result = Entrez.efetch(db="protein", id=ncbi_id, rettype="genbank", retmode='text')
        record = SeqIO.read(efetch_result, "gb")

        # Get the protein name from the record - was a pain to figure out
        # Set a default of the protein name
        p_name = record.name
        for item in record.features:
            if 'product' in item.qualifiers:
                protein_name = item.qualifiers['product'][0]
                break
        # build up a record of info extracted from genbank record
        fasta_dict[ncbi_id] = {
            "ncbi_id": record.id,
            "description": record.description,
            "sequence": str(record.seq),
            "protein": protein_name,
            "organism": record.annotations['organism']
        }
    except Exception as exc:
        message = f"Failed to retrieve ncbi record for {ncbi_id} > {exc}"
        return_output(message, 1)

return_output(json.dumps(fasta_dict), 0)
