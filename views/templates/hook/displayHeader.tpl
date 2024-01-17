<style>
.thumbnail-top{
    position: relative;
}

.thumbnail-top::after{
    content: '';
    background-image: url({$img});
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    opacity: {$img_opacity};
    {if $img_repeat == 1}

        background-repeat: repeat;
        background-size: 150px;

    {else}

        background-repeat: no-repeat;
        background-size: cover;

    {/if}
}

.product-cover::after{
    content: '';
    background-image: url({$img});
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    opacity: {$img_opacity};

    {if $img_repeat == 1}

        background-repeat: repeat;
        background-size: 150px;
        
    {else}

        background-repeat: no-repeat;
        background-size: cover;     
    
    {/if}
}
</style>