import sys
from PIL import Image, ImageChops
a,b=sys.argv[1],sys.argv[2]
A=Image.open(a).convert('RGB');B=Image.open(b).convert('RGB')
h=min(A.height,B.height); w=min(A.width,B.width)
d=ImageChops.difference(A.crop((0,0,w,h)),B.crop((0,0,w,h)))
bbox=d.getbbox()
px=sum(1 for p in d.get_flattened_data() if max(p)>40)
print(f"{a.split('/')[-1]}: size {A.size}->{B.size} diffpx={px} ({px*100/(w*h):.3f}%) bbox={bbox}")
