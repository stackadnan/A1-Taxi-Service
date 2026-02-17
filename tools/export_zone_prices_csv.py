import os
from pathlib import Path
import pandas as pd

BASE = Path(__file__).resolve().parents[1]
ZONE_DIR = BASE / "zone prices"
OUT_DIR = BASE / "storage" / "app" / "zone_prices"
OUT_DIR.mkdir(parents=True, exist_ok=True)

STANDARD_COLS = ["zone_name", "saloon_price", "business_price", "mpv6_price", "mpv8_price"]

def find_header_row(df):
    # look for a row containing 'Zone' or 'Zone Name' (case-insensitive)
    for i, row in df.iterrows():
        joined = " ".join([str(x).strip() for x in row.values if pd.notna(x)])
        if 'zone name' in joined.lower() or 'zone' in joined.lower() and 'price' not in joined.lower():
            return i
    # fallback: if second row looks like header, return 1, else 0
    if df.shape[0] > 1:
        return 1
    return 0


def normalize_df(df_raw):
    header_idx = find_header_row(df_raw)
    header = df_raw.iloc[header_idx].fillna('').astype(str).tolist()
    data = df_raw.iloc[header_idx+1:].copy()
    data.columns = [h.strip() for h in header]

    # find zone column
    zone_col = None
    for c in data.columns:
        if 'zone' in c.lower():
            zone_col = c
            break
    if zone_col is None:
        zone_col = data.columns[0]

    # map price columns
    def find_col_like(key):
        for c in data.columns:
            if key in c.lower():
                return c
        return None

    saloon = find_col_like('saloon') or find_col_like('salon') or find_col_like('sal')
    business = find_col_like('business') or find_col_like('biz')
    mpv6 = find_col_like('mpv6') or find_col_like('mpv 6') or find_col_like('mpv')
    mpv8 = find_col_like('mpv8') or find_col_like('mpv 8')

    out = pd.DataFrame()
    out['zone_name'] = data[zone_col].astype(str).str.strip()

    def conv(col):
        if col is None:
            return pd.Series([None] * len(out))
        return pd.to_numeric(data[col].replace(['', 'nan', 'None'], pd.NA), errors='coerce')

    out['saloon_price'] = conv(saloon)
    out['business_price'] = conv(business)
    out['mpv6_price'] = conv(mpv6)
    out['mpv8_price'] = conv(mpv8)

    # drop rows missing zone_name or all prices
    out = out[out['zone_name'].str.strip() != '']
    out = out.reset_index(drop=True)
    return out[STANDARD_COLS]


if __name__ == '__main__':
    summary = []
    for path in sorted(ZONE_DIR.glob('*.xlsx')):
        airport = path.stem
        try:
            xls = pd.ExcelFile(path, engine='openpyxl')
            # take first sheet only (sheets look uniform)
            sheet = xls.sheet_names[0]
            df_raw = pd.read_excel(path, sheet_name=sheet, header=None, engine='openpyxl', dtype=str)
            norm = normalize_df(df_raw)
            out_path = OUT_DIR / f"{airport}.csv"
            norm.to_csv(out_path, index=False)
            summary.append({'file': path.name, 'rows': int(norm.shape[0]), 'out_csv': str(out_path)})
        except Exception as e:
            summary.append({'file': path.name, 'error': str(e)})

    print('Export summary:')
    for s in summary:
        print(s)
    print('\nSaved normalized CSVs to', OUT_DIR)
