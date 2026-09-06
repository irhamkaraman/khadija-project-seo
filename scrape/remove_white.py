from PIL import Image
import numpy as np

def make_transparent_and_crop(input_path, output_path, make_square=False):
    img = Image.open(input_path).convert("RGBA")
    data = np.array(img)
    
    r, g, b, a = data[:,:,0], data[:,:,1], data[:,:,2], data[:,:,3]
    
    # Threshold for white
    white_mask = (r > 235) & (g > 235) & (b > 235)
    data[white_mask, 3] = 0
    
    img_transparent = Image.fromarray(data)
    
    # Crop to bounding box
    bbox = img_transparent.getbbox()
    if bbox:
        img_transparent = img_transparent.crop(bbox)
        
    if make_square:
        # Pad to make it a perfect square
        width, height = img_transparent.size
        max_dim = max(width, height)
        square_img = Image.new("RGBA", (max_dim, max_dim), (0, 0, 0, 0))
        offset = ((max_dim - width) // 2, (max_dim - height) // 2)
        square_img.paste(img_transparent, offset)
        square_img.thumbnail((256, 256), Image.Resampling.LANCZOS)
        square_img.save(output_path, "PNG")
    else:
        img_transparent.save(output_path, "PNG")
    print(f"Saved {output_path}")

input_img = r"C:\Users\HUAWEI\.gemini\antigravity-ide\brain\755f05c9-98ba-484d-9ecb-c3039cb7be0f\sampein_aja_icon_1788691415907.jpg"

make_transparent_and_crop(input_img, r"c:\Users\HUAWEI\Desktop\Belajar Laravel\khadija-project-seo\public\images\logo_icon.png", make_square=False)
make_transparent_and_crop(input_img, r"c:\Users\HUAWEI\Desktop\Belajar Laravel\khadija-project-seo\public\favicon.png", make_square=True)
