<style>
.thumbnail-top{
    position: relative;
}

.thumbnail-top::after{
    content: '';
    background-image: url({$img});
    background-repeat: repeat;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    opacity: 0.5;
}

.product-cover::after{
    content: '';
    background-image: url({$img});
    background-repeat: repeat;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
</style>