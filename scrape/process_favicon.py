from PIL import Image, ImageDraw
import sys
import shutil
import os

def make_circular_favicon(input_path, output_path):
    img = Image.open(input_path).convert("RGBA")
    
    # Create a circular mask
    mask = Image.new('L', img.size, 0)
    draw = ImageDraw.Draw(mask)
    draw.ellipse((0, 0) + img.size, fill=255)
    
    # Apply mask
    output = Image.new('RGBA', img.size, (0, 0, 0, 0))
    output.paste(img, (0, 0), mask=mask)
    
    # Resize to favicon size
    output.thumbnail((256, 256), Image.Resampling.LANCZOS)
    output.save(output_path, format="PNG")
    print(f"Saved favicon to {output_path}")

# Ensure public/images exists
os.makedirs(r"c:\Users\HUAWEI\Desktop\Belajar Laravel\khadija-project-seo\public\images", exist_ok=True)

make_circular_favicon(
    r"C:\Users\HUAWEI\.gemini\antigravity-ide\brain\755f05c9-98ba-484d-9ecb-c3039cb7be0f\sampein_aja_icon_1788691415907.jpg",
    r"c:\Users\HUAWEI\Desktop\Belajar Laravel\khadija-project-seo\public\favicon.png"
)

shutil.copy(
    r"C:\Users\HUAWEI\.gemini\antigravity-ide\brain\755f05c9-98ba-484d-9ecb-c3039cb7be0f\sampein_aja_full_logo_1788691444858.jpg",
    r"c:\Users\HUAWEI\Desktop\Belajar Laravel\khadija-project-seo\public\images\logo.jpg"
)
print("Copied full logo.")
