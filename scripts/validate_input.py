from Bio import Entrez
import subprocess
import sys
import json
from base64 import b64encode

# Use this when we want to exit so we can display error messages
def return_output(my_message, my_status=1, my_ids=None, hash_id=None):
    print(json.dumps({
        'message': my_message,
        'ids': my_ids,
        'status': my_status,
        'hash': hash_id}))
    exit()

# api key is a system related variable, I've set this in my .bashrc file. You'll need to set this yourself or change the value if running elsewhere
Entrez.email = "s2891770@ed.ac.uk"
Entrez.api_key = subprocess.check_output("echo ${NCBI_API_KEY}", shell=True).rstrip().decode('utf-8')

# Switching between local development and development on the msc8 server. Locally I have api key set to env_var, can't seem to get this working the same way on msc8 so having to resort to reading from file
if not Entrez.api_key:
    with open('/localdisk/home/s2891770/.secrets/ncbi_api_key', 'r') as key:
        Entrez.api_key = key.read().strip()

# Valdiate that both have been set, if not then we won't continue
if not Entrez.email or not Entrez.api_key:
    message = "Your Entrez email or api key haven't been defined, please contact the site creator to inform them to fix this"
    return_output(message)

# Get input params - validation for this done in php script
# I know it's a bit tightly coupled by not having separate validation here but seems somewhat redundant
protein = sys.argv[1]
organism = sys.argv[2]
# print(f"Retrieved inputs - protein = {protein} organism = {organism}")

# We're specifically interested in the protein db for this case
# Search for complete records including our search terms
try:
    esearch_result = Entrez.esearch(db="protein", term=f"{organism}[Organism] AND {protein} AND complete", retmax=10)
    esearch_result = Entrez.read(esearch_result)
    # print(f"Successfully performed esearch for '{organism}[Organism] AND {protein} AND complete'")
except Exception as exc:
    message = f"Failed to conduct esearch for '{organism}[Organism] and {protein} and complete' > {exc}"
    return_output(message)

# Return final output
if len(esearch_result['IdList']) < 2:
    message = (f"Sorry, it looks like there aren't enough complete {organism} {protein} entries in the NCBI protein database. \nPlease check your inputs or try a different query.")
    status = 1
    return_output(message)
else:
    message = (f"There are {esearch_result['Count']} complete {organism} {protein} entries in the protein db")
    status = 0
    ncbi_ids = sorted(esearch_result['IdList'])
    # I'm hashing the sorted list here so that we can have unique primary keys in table representing specific searches. - if this works I'm going to be very pleased with myself!!!
    search_id = b64encode(json.dumps(ncbi_ids).encode()).decode()
    return_output(message, status, ncbi_ids, search_id)
