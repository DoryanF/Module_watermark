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
            width: 100%;
            height: 100%;
        }
    }
{/if}
</style>

<script>

document.addEventListener("DOMContentLoaded", () => {

    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        alert("Clic droit désactivé !");
    });


    document.addEventListener("keydown", function(e) {
        console.log(e);
        if(e.key == 'F12')
        {
            e.stopPropagation();
            e.preventDefault();
            window.event.cancelBubble = true;
            alert("F12 désactivé !")
        }
    });

});
</script>