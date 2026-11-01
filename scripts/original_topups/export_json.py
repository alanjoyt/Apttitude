import json
import os

from gen_infosys import get_infosys
from gen_wipro import get_wipro
from gen_accenture import get_accenture
from gen_cognizant import get_cognizant
from gen_capgemini import get_capgemini
from gen_deloitte import get_deloitte
from gen_tech_mahindra_zoho import get_tech_mahindra, get_zoho
from gen_lti_hcl import get_lti_mindtree, get_hcl
from gen_amazon_ust_ibm import get_amazon, get_ust, get_ibm

DATA_DIR = os.path.join(os.path.dirname(__file__), "data")
os.makedirs(DATA_DIR, exist_ok=True)

generators = {
    "Infosys": get_infosys,
    "Wipro NLTH": get_wipro,
    "Accenture": get_accenture,
    "Cognizant": get_cognizant,
    "Capgemini": get_capgemini,
    "Deloitte": get_deloitte,
    "Tech Mahindra": get_tech_mahindra,
    "Zoho": get_zoho,
    "LTI Mindtree": get_lti_mindtree,
    "HCL": get_hcl,
    "Amazon": get_amazon,
    "UST": get_ust,
    "IBM": get_ibm,
}

total_qs = 0
for comp, fn in generators.items():
    print(f"Generating and verifying {comp}...")
    qs = fn()
    path = os.path.join(DATA_DIR, f"{comp.replace(' ', '_').lower()}.json")
    with open(path, "w", encoding="utf-8") as f:
        json.dump(qs, f, indent=2)
    print(f"  -> Saved {len(qs)} questions to {path}")
    total_qs += len(qs)

print(f"\nAll {total_qs} topup questions successfully generated, verified, and exported to {DATA_DIR}!")
