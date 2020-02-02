# Terraforming with MilsGen & MilsStitcher

![Intro Image](https://github.com/Unitron-Galactic/Tools-Workflows/blob/master/Terraforming%201/images/Terraforming_Preview.jpg)

These instructions guide you from gathering terrain data to the generation of an appropriate LEGO Model.

## Table of contents

1. [Requirements](#requirements)
2. [Data Sources](#data-sources)
3. [Heightmap Generation](#heightmap-generation)
4. [Module Generation](#module-generation)
5. [Module Stitching](#module-stitching)
6. [Render Optimisation](#render-optimisation)
7. [F.A.Q.](#faq)

## Requirements

* Windows PC or VM (Possibly: VC++ package ([freely downloadable from Microsoft](http://www.microsoft.com/en-us/download/details.aspx?id=5555))
* PHP 5.3 or newer ([Freely available on php.net](https://windows.php.net/download/))
* [MeshLab](http://www.meshlab.net/), Blender of any program to generate a heightmap from 3D data.
* Possibly an image/photo editor for resizing.
* Any LDR-compatible Lego CAD program.

## Data Sources

First, you need some base data of terrain you’d like to generate. If you can generate an artificial landscape yourself you may skip data acquisition and straight-up continue with your .stl or .obj file.
Otherwise here are some places that let you export 3D models from real data.

### Earth

There is a wide variety of free services to generate 3D data from map data like google maps. They vary in resolution and quality.

An incomplete list:

* [Terrain 2 STL](http://jthatch.com/Terrain2STL/)
* [Map 2 STL](https://map2stl.com/)
* [TouchTerrain](https://touchterrain.geol.iastate.edu/)
* [The Terrainator*](https://terrainator.com/)

*Paid Option

### Celestial Bodies

Humankind also mapped some celestial bodies. Free science data is available!

An incomplete list:

* [NASA TREK](https://trek.nasa.gov/) - Mapped planets: Mercury, Mars; Mapped moons: Moon, Titan; Mapped bodies: Ceres, Vesta
* [NASA 3D Ressources](https://nasa3d.arc.nasa.gov/models) - Mapped Things: A lot!
* [ESA Planetary Science Archive](https://archives.esac.esa.int/psa/#!Home%20View) - Mapped Things: unknown
* [The Terrainator*](https://terrainator.com/) - Mapped Objects: Mars, Moon

*Paid Option

### Artificial

It is also possible to generate non-existent landscapes.

* [Terragen](https://planetside.co.uk/)

## Heightmap Generation

To properly generate your Model you need the height data of your terrain. A heightmap or heightfield is a raster image used mainly as Discrete Global Grid. Each pixel store values, such as surface elevation data, for display in 3D computer graphics. A heightmap contains one channel interpreted as a distance of displacement or "height" from the "floor" of a surface and sometimes visualized as luma of a grayscale image, with black representing minimum height and white representing maximum height.

![Sample Heightmap generated from the iconic Moon Baseplate](https://github.com/Unitron-Galactic/Blueprints/blob/master/Terrain/Tiles/Moon_Baseplate/depthmaps/Baseplate_1_192.png)

The basic workflow positions an orthogonal camera on top of the terrain that’s only fed from the Z-Buffer of the 3D model.
Depending upon the software you choose there are slightly different ways to achieve this:

* [Meshlab - Written Tutorial](https://community.glowforge.com/t/tutorial-creating-a-depth-map-from-a-3d-model-for-3d-engraving/6659) - Probably the fastest way. Less than 10 clicks.
* [Blender - Written Tutorial](https://www.sjg.io/post/75382046691/creating-bump-maps-for-texture-engraving-from-stl)
* [Blender - Youtube Tutorial](https://www.youtube.com/watch?v=dUEPieo26nk)
* [Blender - Youtube Tutorial 2](https://www.youtube.com/watch?v=AtkU2aaaCfU)

## Module Generation

To generate terrain modules from your hightmap use [MILSGen](http://www.legoism.info/2013/12/milsgen.html) by [Oton/Legoism](www.legoism.info).

**Note:** If you generate terrain only for rendering, disable Substructure and skip step 5.

1. Modify the image size of the heightmap to multiples of 32x32pixels. It should be in PNG, BMP or JPEG format, 24-bit color (i.e. not paletted), and at least 32x32 pixels in size. If the dimensions are not multiples of 32, MilsGen will automatically crop the available area to the top left corner. 
Keep in mind that each pixel corresponds to one stud in the output! That is, something that may seem like a moderately large image on the screen (say, 1024x768 pixels) will result in 768 modules, over 8x6 meters in size. Rescale your input images using any of the many freeware image editing and viewing programs.
2. Enter the input terrain image
Simply enter the full name (with extension) of the input image you prefer to use. It should comply with the rules mentioned earlier. If you just hit Enter, the default name milsgen.png will be used, which already exists in the original ZIP as an example.
3. Enter the highest altitude of the modules in tiles, corresponding to the highest point of the image
Upon start, the program will check out the highest point given in the input map (that is, the brightest pixel in the area). Here you can specify how high do you prefer that point to be in the resulting MILS module, using plates (1/3 of a brick) as units. Black color represents zero altitudes, and all other altitudes will be linearly scaled in between. By default (just hit Enter), this height will be 24, or 8 bricks.
4. Include substructure
You can choose whether to include the MILS substructure (baseplate, corner bricks and Technic brick module connectors) in the resulting files. It is by default (just hit Enter) enabled. Type N to disable it, which will result in LDR files being written without this substructure, just the terrain.
5. Add edge identifiers
If you have enabled substructure, you can choose to add edge identifiers which are enabled by default. They will add a simple set of 1x1 bricks in the substructure at their edges that connect to any other modules (i.e. are not at the complete edges of the landscape). These will precisely match their one unique code at the corresponding connecting side of their neighbor. That way, even very shuffled modules can be easily assembled.

[More detailed information in the MilsGenerator Readme.](https://github.com/Unitron-Galactic/Tools-Workflows/blob/master/Terraforming%201/Toolset/MilsGen/Milsgen-Readme.pdf)

## Module Stitching

After running MILSGen you are left with a folder full of module files. Stitching them together is tedious especially with larger terrain. Manually combining dozens of Modules is not an option here.
[MILSStitcher](https://github.com/Unitron-Galactic/Tools-Workflows/tree/master/Terraforming%201/Toolset/MilsStitcher) will help you piece together all .ldr modules to one single .mpd file.

For the script to run, make sure you have PHP set up on your machine. 

Open your Commandline/Terminal and run MILSstitcher.php:
```
php -f MILSstitcher.php
```

The Stitcher will only ask for the directory of the path which contains all the Module data. Anything else happens automatically.
A .mpd file with the name of your folder will be generated.

Sample Output:
```
Please enter path to the directory containing MILS tiles: ~/git/Blueprints/Terrain/Tiles/Moon_Baseplate/Modules_64x64x14p/

The path ~/git/Blueprints/Terrain/Tiles/Moon_Baseplate/Modules_64x64x14p/ exists

Module-List found! 

Tilelist spliced! 
Your area consists of: 2x2 MILS Modules!

Tile-Map extracted... 
```
…
```
Initial Matrix: 
[A,1] [A,2] 
[B,1] [B,2] 

Found Pivot Field at: [A, 1]

Old Field Value: [A, 1]
New Field Value: [0, 0]

Old Field Value: [A, 2]
New Field Value: [0, 1]

Old Field Value: [B, 1]
New Field Value: [1, 0]

Old Field Value: [B, 2]
New Field Value: [1, 1]


Pivot Multiplyer Matrix: 
[0,0] [0,1] 
[1,0] [1,1] 

New File - Path: ~/git/Blueprints/Terrain/Tiles/Moon_Baseplate/Modules_64x64x14p/Modules_64x64x14p.mpd
Listing A1 with Matrix-Shift: [0,0] ==> 1 0 0 0 0 1 0 0 0 1 0 0 0 1 A1.ldr
Listing A2 with Matrix-Shift: [0,1] ==> 1 0 0 0 -640 1 0 0 0 1 0 0 0 1 A2.ldr
Listing B1 with Matrix-Shift: [1,0] ==> 1 0 640 0 0 1 0 0 0 1 0 0 0 1 B1.ldr
Listing B2 with Matrix-Shift: [1,1] ==> 1 0 640 0 -640 1 0 0 0 1 0 0 0 1 B2.ldr

Stitching complete!
```

## Render Optimisation

### Module Piece Optimisation
Depending upon your desired terrain height MILSGen may generate files with many pieces that may never be seen in your Render and only clog up RAM. To reduce the piece count you may need to remove them **manually** from each Module. MilsStitcher does not provide such an optimization option.

The reduction is well worth the time. The initial piece count on the 520x520x180p CERES Map was above 467k pieces which could be reduced to about 141k! (Reduction from 21.5MB file size to 6.5MB)

### Color Change
MILSGen only uses green pieces. Use your LCAD-Tool to change the color. If you generate a very large terrain you may use a text editor do globally change all second values in each line from 10 (green) to your desired one. ([LDR Colour Definitions](https://www.ldraw.org/article/547.html) may be helpful for this.)

### General

Delete Modules from the .mpd file that won’t show in your Render.

![Ceres Optimized](https://github.com/Unitron-Galactic/Tools-Workflows/blob/master/Terraforming%201/images/Optimised_Ceres_Map.png)


## F.A.Q.

#### Why php for the Stitcher?
This was basically just a small thing I wrote late at night at a fast-paced weekend. As php is one of my most uses languages it was used to get this done.

#### MilsGen won’t generate my large Landscape, why?
A) Make sure the filename contains only ASCII Symbols. Milsgen does not like whitespaces and funky characters.
B) Your landscape is too big. MilsGen only generates landscapes up to 832x3168 studs/pixels (A1 to Z99).

#### My Editor can’t open the large .mpd
A) Add more RAM
B) Optimize your Modules **before** the .mpd stitching for a lesser piece count.
C) Generate lower terrains. Your height may result in a ridiculous piece count.

#### Can I submit changes to the Stitcher?
Sure, fork the code and make a request! Help is always welcome

#### Where can I show my virtual support?
You may follow Unitron Galactic on [Instagram](https://www.instagram.com/unitron_galactic/) or [Flickr](https://www.flickr.com/people/185934740@N06/).


