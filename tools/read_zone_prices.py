import json
import os
import sys
from pathlib import Path
import pandas as pd

BASE = Path(__file__).resolve().parents[1]
ZONE_DIR = BASE / "zone prices"

result = []

for path in sorted(ZONE_DIR.glob('*.xlsx')):
    file_info = {"filename": path.name, "sheets": []}
    try:
        xls = pd.ExcelFile(path, engine='openpyxl')
        for sheet_name in xls.sheet_names:
            try:
                df = pd.read_excel(xls, sheet_name=sheet_name, engine='openpyxl')
                # Normalize column names to strings
                cols = [str(c) for c in df.columns.tolist()]
                sample = df.head(5).fillna('').to_dict(orient='records')
                file_info['sheets'].append({
                    'sheet_name': sheet_name,
                    'rows': int(df.shape[0]),
                    'cols': cols,
                    'sample_rows': sample,
                })
            except Exception as e:
                file_info['sheets'].append({'sheet_name': sheet_name, 'error': str(e)})
    except Exception as e:
        file_info['error'] = str(e)

    result.append(file_info)

print(json.dumps(result, indent=2, ensure_ascii=False))
