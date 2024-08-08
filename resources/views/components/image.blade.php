@php

    use Cloudinary\Transformation\Effect;
    use Cloudinary\Transformation\Resize;
    use Cloudinary\Transformation\Rotate;
    use Cloudinary\Transformation\Argument\Color;
    use Cloudinary\Transformation\RoundCorners;
    use Cloudinary\Transformation\Border;
    use Cloudinary\Transformation\Gravity;
    use Cloudinary\Transformation\FocusOn;
    use Cloudinary\Transformation\Compass;
    use Cloudinary\Transformation\Reshape;
    use Cloudinary\Transformation\SimulateColorBlind;
    use Cloudinary\Transformation\Adjust;
    use Cloudinary\Transformation\ImproveMode;
    use Illuminate\Support\Str;


    $retrieveFormattedImage = cloudinary()->getImageTag($publicId ?? '');

    /**
     * SET ALT if provided
     */
    if(isset($alt)) {
        $retrieveFormattedImage = cloudinary()->getImageTag($publicId ?? '')
            ->setAttributes([
               'alt' => $alt ?? $publicId
            ]);
    }

    /**
     *  SET CLASS if provided
     */
    if(isset($class)) {
        $retrieveFormattedImage = cloudinary()->getImageTag($publicId ?? '')
            ->setAttributes([
               'class' => $class
            ]);
    }

    /**
    *  If the attribute is "crop"
    */
    if(isset($crop)) {

        $cropOptions = ['thumb', 'crop', 'fill', 'limit', 'scale', 'fit', 'pad', 'lfill', 'lpad', 'mfit', 'mpad', 'scale'];

        if(in_array($crop, $cropOptions)) {

            $cropFactor = $crop;

            if($crop == 'thumb') {
                $cropFactor = 'thumbnail';
            }

            if($crop == 'limit') {
                $cropFactor = 'limitFit';
            }

            if($crop == 'lfill') {
                $cropFactor = 'limitFill';
            }

            if($crop == 'lpad') {
                $cropFactor = 'limitPad';
            }

            if($crop == 'mfit') {
                $cropFactor = 'minimumFit';
            }

            if($crop == 'mpad') {
                $cropFactor = 'minimumPad';
            }

            $retrieveFormattedImage = cloudinary()->getImageTag($publicId ?? '')->resize(
                Resize::$cropFactor()->width($width ?? '')->height($height ?? '')
            );

            /**
             *  If the attribute is "gravity"
             *
             *  Gravity only be used with cropping, most especially thumb cropping
             *
             *  A qualifier that determines which part of an asset to focus on, and thus which part of the asset to keep, when any part of the asset is
             *  cropped. For overlays, this setting determines where to place the overlay.
             */
            if(isset($gravity)) {

                /**
                *  Check if the gravity attribute is a compass gravity
                */
                if(Str::startsWith($gravity, 'compass:')) {
                    $gravityBreakdown = explode(':', $gravity);
                    $gravityConstant   =  Str::studly($gravityBreakdown[1]);

                    $gravityImplementation =  Resize::$cropFactor()->width($width ?? '')->height($height ?? '')->gravity(
                        Gravity::compass(Compass::$gravityConstant())
                    );
                } else {

                    /**
                     * Check if its a face, faces or object gravity. e.g faces, face, microwave
                     */
                    $gravityImplementation =  Resize::$cropFactor()->width($width ?? '')->height($height ?? '')->gravity(
                        Gravity::focusOn(FocusOn::$gravity())
                    );
                }

                $retrieveFormattedImage = cloudinary()->getImageTag($publicId ?? '')->resize(
                    $gravityImplementation
                );
            }
        }
    }

    /**
    *  If the attribute is "effect"
    */
    if(isset($effect)) {

        if($effect == 'cartoonify') {
            $retrieveFormattedImage->effect(Effect::cartoonify());
        }

        if(str_contains($effect, 'cartoonify|')) {

            $cartoonification = explode('|', $effect);

            $cartoonQualifier = explode(':', $cartoonification[1]);

            $lineStrength        = $cartoonQualifier[0];
            $colorReductionLevel = $cartoonQualifier[1];

            if($colorReductionLevel == 'bw') {
                $retrieveFormattedImage->effect(Effect::cartoonify()->lineStrength($lineStrength)->blackwhite());
            }

            $retrieveFormattedImage->effect(Effect::cartoonify()->lineStrength($lineStrength)->colorReductionLevel($colorReductionLevel));

        }

        if(str_contains($effect, 'art|')) {

            $artisticFilter = explode('|', $effect);

            $artQualifier = $artisticFilter[1];

            $retrieveFormattedImage->effect(Effect::artisticFilter($artQualifier));

        }
    }

    /**
    *  If the attribute is "rotate"
    */
    if(isset($rotate)) {
        $retrieveFormattedImage->rotate(Rotate::byAngle($rotate));
    }

    /**
    *  If the attribute is "colorize"
    */
    if(isset($colorize)) {

        /** Example of values can be:

        - violet_50
        - rgb:999_30

        **/
        if(gettype($colorize) == 'string') {

            $colorizeAttributes = explode('_', $colorize);

            $color = $colorizeAttributes[0];
            $level = $colorizeAttributes[1];

            /**
             *  SET COLOR FORMAT depending on rgb format or normal color format
             */
            if(Str::startsWith($color, 'rgb:')) {
               $colorBreakdown = explode(':', $color);
               $colorFormat    = Color::rgb($colorBreakdown[1]);
            } else {
                $colorFormat = Color::$color();
            }

            $retrieveFormattedImage->effect(Effect::colorize()->level($level)->color($colorFormat));
        }
    }

    /**
    *  If the attribute is "trim"
    */
    if(isset($trim)) {

        /** Example of values can be:

        - 50
        - 50_yellow

        **/
        if(gettype($trim) == 'string') {

            $trimAttributes = explode('_', $trim);

            $colorSimilarity = $trimAttributes[0];
            $colorOverride   = $trimAttributes[1];

            /**
             *  SET COLOR FORMAT depending on rgb format or normal color format
             */
            if(Str::startsWith($colorOverride, 'rgb:')) {
               $colorBreakdown = explode(':', $colorOverride);
               $colorFormat    = Color::rgb($colorBreakdown[1]);
            } else {
                $colorFormat = Color::$colorOverride();
            }

            $retrieveFormattedImage->reshape(Reshape::trim()->colorSimilarity($colorSimilarity)->colorOverride($colorFormat));
        }
    }

    /**
    *  If the attribute is "blur"
    */
    if(isset($blur)) {

        /** Example of values can be:

        - 20000
        **/
        $retrieveFormattedImage->effect(Effect::blur()->strength($blur));
    }

    /**
    *  If the attribute is "grayscale"
    */
    if(isset($grayscale)) {
        $retrieveFormattedImage->effect(Effect::grayscale());
    }

    /**
    *  If the attribute is "blackwhite"
    */
    if(isset($blackwhite)) {

        if(gettype($blackwhite) == 'string') {

            $threshold = $blackwhite;
            $retrieveFormattedImage->effect(Effect::blackwhite()->threshold($threshold));
        }

        $retrieveFormattedImage->effect(Effect::blackwhite());
    }

    /**
    *  If the attribute is "sepia"
    */
    if(isset($sepia)) {
        $retrieveFormattedImage->effect(Effect::sepia()->level($sepia));
    }

    /**
    *  If the attribute is "redeye"
    */
    if(isset($redeye)) {
        $retrieveFormattedImage->effect(Effect::redEye());
    }

    /**
    *  If the attribute is "negate"
    */
    if(isset($negate)) {
        $retrieveFormattedImage->effect(Effect::negate());
    }

    /**
    *  If the attribute is "oil-paint"
    */
    if(isset($oilPaint)) {
        $retrieveFormattedImage->effect(Effect::oilPaint()->strength($oilPaint));
    }

    /**
    *  If the attribute is "vignette"
    */
    if(isset($vignette)) {
        $retrieveFormattedImage->effect(Effect::vignette()->strength($vignette));
    }

    /**
    *  If the attribute is "simulate-colorblind"
    */
    if(isset($simulateColorblind)) {

        if(gettype($simulateColorblind) == 'boolean') {
            $value = SimulateColorBlind::deuteranopia();
        } else {
            $value = SimulateColorBlind::$simulateColorblind();
        }

        $retrieveFormattedImage->effect(Effect::simulateColorblind()->condition($value));
    }

    /**
    *  If the attribute is "pixelate"
    */
    if(isset($pixelate)) {
        $retrieveFormattedImage->effect(Effect::pixelate()->squareSize($pixelate));
    }

    /**
    *  If the attribute is "unsharp-mask"
    */
    if(isset($unsharpMask)) {
        $retrieveFormattedImage->adjust(Adjust::unsharpMask()->strength($unsharpMask));
    }

    /**
    *  If the attribute is "saturation"
    */
    if(isset($saturation)) {
        $retrieveFormattedImage->adjust(Adjust::saturation()->level($saturation));
    }

    /**
    *  If the attribute is "contrast"
    */
    if(isset($contrast)) {
        $retrieveFormattedImage->adjust(Adjust::contrast()->level($contrast));
    }

    /**
    *  If the attribute is "brightness"
    */
    if(isset($brightness)) {
        $retrieveFormattedImage->adjust(Adjust::brightness()->level($brightness));
    }

    /**
    *  If the attribute is "gamma"
    */
    if(isset($gamma)) {
        $retrieveFormattedImage->adjust(Adjust::gamma()->level($gamma));
    }

    /**
    *  If the attribute is "improve-mode"
    */
    if(isset($improveMode)) {

        /** Example of values can be 'indoor_99'
        *
        *  Mode is 'indoor' or 'outdoor'
        *  Blend is 99
        */

        if(gettype($improveMode) == 'string') {

            $improveAttributes = explode('_', $improveMode);
            $mode  = $improveAttributes[0];
            $blend = $improveAttributes[1];

            $retrieveFormattedImage->adjust(
                Adjust::improve()->mode(ImproveMode::$mode())->blend($blend));

        }
    }


    /**
    *  If the attribute is "shadow"
    */
    if(isset($shadow)) {

        /** Example of values can be:

        - rgb:999_-15_-15_50
        - color_offsetX_offsetY_strength

        **/
        if(gettype($shadow) == 'string') {

            $shadowAttributes = explode('_', $shadow);

            $color    = $shadowAttributes[0];
            $offsetX  = $shadowAttributes[1];
            $offsetY  = $shadowAttributes[2];
            $strength = $shadowAttributes[3];

            /**
             *  SET COLOR FORMAT depending on rgb format or normal color format
             */
            if(Str::startsWith($color, 'rgb:')) {
               $colorBreakdown = explode(':', $color);
               $colorFormat    = Color::rgb($colorBreakdown[1]);
            } else {
                $colorFormat = Color::$color();
            }

            $retrieveFormattedImage->effect(
                Effect::shadow()->strength($strength)
                    ->color($colorFormat)
                    ->offsetX($offsetX)
                    ->offsetY($offsetY)
            );
        }
    }

    /**
    *  If the attribute is "border"
    */
    if(isset($border)) {
        /** Example of values can be:

        - 40_solid_brown
        - 40_solid_rgb:999

        **/
        if(gettype($border) == 'string') {

            $borderAttributes = explode('_', $border);

            $width = $borderAttributes[0];
            $style = $borderAttributes[1];
            $color = $borderAttributes[2];

            /**
             *  SET COLOR FORMAT depending on rgb format or normal color format
             */
            if(Str::startsWith($color, 'rgb:')) {
               $colorBreakdown = explode(':', $color);
               $colorFormat    = Color::rgb($colorBreakdown[1]);
            } else {
                $colorFormat = Color::$color();
            }

            $retrieveFormattedImage->border(Border::$style()->width($width)->color($colorFormat));
        }
    }

    /**
    *  If the attribute is "round-corners"
    */
    if(isset($roundCorners)) {
        if(gettype($roundCorners) == 'boolean') {
            $retrieveFormattedImage->roundCorners(RoundCorners::max());
        }
    }

    /**
    *  If the attribute is "bg-color" a.k.a Background Color
    */
    if(isset($bgColor)) {
        $retrieveFormattedImage->backgroundColor(Color::$bgColor());
    }

    /**
     *  If the attribute is "art"
     */
    if(isset($art)) {
        $retrieveFormattedImage->effect(Effect::artisticFilter($art));
    }

    /**
     *  If the attribute is "cartoonify"
     */
    if(isset($cartoonify)) {

        if(gettype($cartoonify) == 'boolean') {
            $retrieveFormattedImage->effect(Effect::cartoonify());
        }

        if(gettype($cartoonify) == 'string') {

            $cartoonQualifier = explode(':', $cartoonify);

            $lineStrength        = $cartoonQualifier[0];
            $colorReductionLevel = $cartoonQualifier[1];

            if($colorReductionLevel == 'bw') {
                $retrieveFormattedImage->effect(Effect::cartoonify()->lineStrength($lineStrength)->blackwhite());
            } else {
                $retrieveFormattedImage->effect(Effect::cartoonify()->lineStrength($lineStrength)->colorReductionLevel($colorReductionLevel));
            }
        }
    }

    echo $retrieveFormattedImage->serialize();

@endphp