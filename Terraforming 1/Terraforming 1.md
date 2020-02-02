# Terraforming with MilsGen & MilsStitcher

![Intro Image](https://github.com/Unitron-Galactic/Tools-Workflows/blob/master/Terraforming%201/images/Terraforming_Preview.jpg „Benny speeding over a Ceres“)

These instructions guide you from gathering terrain data to the generation of an appropriate LEGO Model.

## Table of contents

1. [Requirements](#requirements)
2. [Data Sources](#datasources)
3. [Heightmap Generation](#heightmap)
4. [Module Generation](#milsgen)
5. [Module Stitching](#milsstitcher)
6. [Render Optimisation ](#optimisation)
7. [F.A.Q.](#faq)

## Requirements <a name=„requirements“></a>

* Windows PC or VM (Possibly: VC++ package ([freely downloadable from Microsoft](http://www.microsoft.com/en-us/download/details.aspx?id=5555))
* PHP 5.3 or newer ([Freely available on php.net](https://windows.php.net/download/))
* [MeshLab](http://www.meshlab.net/), Blender of any program to generate a heightmap from 3D data.
* Possibly an image/photo editor for resizing.
* Any LDR-compatible Lego CAD program.

## Data Sources <a name=„datasources“></a>

First you need some base data of a terrain you’d like to generate. If you can generate an artificial landscape yourself you may skip data acquisition and straight up continue with your .stl or .obj file.
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

It is also possible to generate non existent landscapes.

* [Terragen](https://planetside.co.uk/)

## Heightmap Generation <a name=„heightmap“></a>

In order to propperly generate your Model you need the height data of your terrain. A heightmap or heightfield is a raster image used mainly as Discrete Global Grid. Each pixel store values, such as surface elevation data, for display in 3D computer graphics. A heightmap contains one channel interpreted as a distance of displacement or "height" from the "floor" of a surface and sometimes visualized as luma of a grayscale image, with black representing minimum height and white representing maximum height.

The basic workflow positions an orthogonal camera on top of the terrain that’s only fed from the Z Buffer of the 3D model.
Depending upon the software you choose there are slightly different ways to achieve this:

* [Meshlab - Written Tutorial](https://community.glowforge.com/t/tutorial-creating-a-depth-map-from-a-3d-model-for-3d-engraving/6659) - Probably the fastest way. Less than 10 clicks.
* [Blender - Written Tutorial](https://www.sjg.io/post/75382046691/creating-bump-maps-for-texture-engraving-from-stl)
* [Blender - Youtube Tutorial](https://www.youtube.com/watch?v=dUEPieo26nk)
* [Blender - Youtube Tutorial 2](https://www.youtube.com/watch?v=AtkU2aaaCfU)


![Sample Heightmap](https://github.com/Unitron-Galactic/Blueprints/blob/master/Terrain/Other/Ceres_1_NorthWest/depthmaps/Ceres1_depthmap_520x520.png „A sample heightmap generated from Ceres“)
A sample heightmap generated from Ceres.

## Module Generation <a name=„milsgen“></a>

1. Modify the image size of the heightmap to multiples of 32x32pixels. It should be in PNG, BMP or JPEG format, 24-bit colour (i.e. not paletted), and at least 32x32 pixels in size. If the dimensions are not multiples of 32, MilsGen will automatically crop the available area to the top left corner. 
Keep in mind that each pixel corresponds to one stud in the output! That is, something that may seem as a moderately large image on the screen (say, 1024x768 pixels) will result in 768 modules, over 8x6 meters in size. Rescale your input images using any of the many freeware image editing and viewing programs.
2. 

## Module Stitching <a name=„milsstitcher“></a>

## Render Optimisation <a name=„optimisation“></a>

## F.A.Q. <a name=„Fan“></a>




