<style>
.thumbnail-top{
    position: relative;
}

.thumbnail-top::after{
    content: '';
    background-image: url({$img});
    width: {$img_size};
    height: {$img_size};
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
    width: {$img_size};
    height: {$img_size};
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

@media only screen and (max-width: 767px) {
    .thumbnail-top::after{
        top: 40px;
        left: 40px;
        width: 80%;
        height: 80%;
    }
    
}

{if $img_repeat == 1}
    @media only screen and (max-width: 767px){
        .thumbnail-top::after{
            top: 0;
            left: 0;
            width: {$img_size};
            height: {$img_size};
        }
    }
{/if}
</style>