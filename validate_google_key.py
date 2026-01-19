import requests
import sys
import json
from datetime import datetime

API_TESTS = {
    "Geocoding": {
        "url": "https://maps.googleapis.com/maps/api/geocode/json",
        "params": {"address": "New York"}
    },
    "Places": {
        "url": "https://maps.googleapis.com/maps/api/place/nearbysearch/json",
        "params": {
            "location": "40.7128,-74.0060",
            "radius": "500",
            "type": "restaurant"
        }
    },
    "Directions": {
        "url": "https://maps.googleapis.com/maps/api/directions/json",
        "params": {
            "origin": "New York",
            "destination": "Boston"
        }
    },
    "Distance Matrix": {
        "url": "https://maps.googleapis.com/maps/api/distancematrix/json",
        "params": {
            "origins": "New York",
            "destinations": "Boston"
        }
    }
}


def test_api(name, config, api_key):
    params = config["params"].copy()
    params["key"] = api_key

    try:
        r = requests.get(config["url"], params=params, timeout=10)
        data = r.json()

        status = data.get("status")

        result = {
            "api": name,
            "status": status,
            "success": False,
            "message": ""
        }

        if status == "OK":
            result["success"] = True
            result["message"] = "API working"

        elif status == "REQUEST_DENIED":
            result["message"] = data.get("error_message", "Access denied")

        elif status == "OVER_QUERY_LIMIT":
            result["message"] = "Quota exceeded"

        elif status == "ZERO_RESULTS":
            result["success"] = True
            result["message"] = "Valid key but no data"

        else:
            result["message"] = f"Unknown status: {status}"

        return result

    except Exception as e:
        return {
            "api": name,
            "status": "ERROR",
            "success": False,
            "message": str(e)
        }


def validate_key(api_key):
    print("\n🔎 GOOGLE MAPS API VALIDATOR\n")

    report = {
        "api_key": api_key,
        "checked_at": str(datetime.now()),
        "results": []
    }

    for name, config in API_TESTS.items():
        print(f"Testing {name}...")

        result = test_api(name, config, api_key)
        report["results"].append(result)

        if result["success"]:
            print("  ✅", result["message"])
        else:
            print("  ❌", result["message"])

    return report


def save_report(report):
    filename = "google_api_report.json"
    with open(filename, "w") as f:
        json.dump(report, f, indent=4)

    print(f"\n📁 Report saved: {filename}")


if __name__ == "__main__":

    if len(sys.argv) != 2:
        print("Usage:")
        print("python validate_google_key.py YOUR_API_KEY")
        sys.exit()

    api_key = sys.argv[1]

    report = validate_key(api_key)
    save_report(report)
