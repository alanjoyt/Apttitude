import os
import sys
import urllib.request
import subprocess
import zipfile

TOOLS_DIR = os.path.join(os.path.dirname(__file__), "..", "tools")
os.makedirs(TOOLS_DIR, exist_ok=True)

def download_file(url, dest_path):
    if os.path.exists(dest_path):
        print(f"Already exists: {dest_path}")
        return
    print(f"Downloading {url} -> {dest_path}...")
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    with urllib.request.urlopen(req) as resp, open(dest_path, 'wb') as f:
        total = int(resp.headers.get('content-length', 0))
        downloaded = 0
        while True:
            chunk = resp.read(1024 * 1024)
            if not chunk:
                break
            f.write(chunk)
            downloaded += len(chunk)
            if total > 0:
                print(f"  {downloaded / (1024*1024):.1f} / {total / (1024*1024):.1f} MB ({(downloaded/total)*100:.1f}%)", end='\r')
        print(f"\nDone: {dest_path}")

def setup_w64devkit():
    url = "https://github.com/skeeto/w64devkit/releases/download/v2.10.1/w64devkit-x64-2.10.1.7z.exe"
    exe_path = os.path.join(TOOLS_DIR, "w64devkit.exe")
    extract_dir = os.path.join(TOOLS_DIR, "w64devkit")
    gcc_path = os.path.join(extract_dir, "bin", "gcc.exe")
    if os.path.exists(gcc_path):
        print(f"w64devkit already installed at {gcc_path}")
        return gcc_path
    download_file(url, exe_path)
    print("Extracting w64devkit...")
    # Self-extracting 7z
    res = subprocess.run([exe_path, f"-o{TOOLS_DIR}", "-y"], capture_output=True, text=True)
    if res.returncode == 0 and os.path.exists(gcc_path):
        print(f"Successfully extracted w64devkit: {gcc_path}")
        return gcc_path
    else:
        print("Extraction failed:", res.stderr)
        return None

def setup_jdk():
    url = "https://github.com/adoptium/temurin21-binaries/releases/download/jdk-21.0.12.1%2B1/OpenJDK21U-jdk_x64_windows_hotspot_21.0.12.1_1.zip"
    zip_path = os.path.join(TOOLS_DIR, "openjdk21.zip")
    extract_dir = os.path.join(TOOLS_DIR, "jdk")
    javac_path = None
    # Check if already extracted
    for root, dirs, files in os.walk(TOOLS_DIR):
        if "javac.exe" in files:
            javac_path = os.path.join(root, "javac.exe")
            print(f"JDK already present at {javac_path}")
            return javac_path

    download_file(url, zip_path)
    print("Unzipping OpenJDK...")
    with zipfile.ZipFile(zip_path, 'r') as zip_ref:
        zip_ref.extractall(extract_dir)
    print("OpenJDK extracted.")
    for root, dirs, files in os.walk(extract_dir):
        if "javac.exe" in files:
            return os.path.join(root, "javac.exe")
    return None

if __name__ == '__main__':
    gcc = setup_w64devkit()
    jdk = setup_jdk()
    print("Compilers ready:", gcc, jdk)

