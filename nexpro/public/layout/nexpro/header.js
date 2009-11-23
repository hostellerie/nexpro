    var leftBlocksWidth;

    function toggleleftblocks(oImg)
    {
        var block = document.getElementById('leftblocks-div');
        var ani = new YAHOO.util.Anim(block);

        var img1 = layout_url + '/images/showleftblocks.gif';
        var img2 = layout_url + '/images/hideleftblocks.gif';

        if (document.getElementById('leftblocks').style.display == 'none') {
            ani.attributes.width = { to: leftBlocksWidth };
            ani.duration = 0.3;
            ani.method = YAHOO.util.Easing.backOut;

            ani.onComplete.subscribe(function () {
                block.style.overflow = 'visible';
            });

            block.style.overflow = 'hidden';
            document.getElementById('leftblocks').style.display = '';
            oImg.src = img2;
            oImg.alt = "Click to hide Left Blocks";
            oImg.title = "Click to hide Left Blocks";
            document.cookie = 'leftblocksmode=show';

            ani.animate();
        } else {
            leftBlocksWidth = document.getElementById('leftblocks').offsetWidth;
            ani.attributes.width = { to: 0 };
            ani.duration = 0.3;
            ani.method = YAHOO.util.Easing.backIn;
            block.style.overflow = 'hidden';

            ani.onComplete.subscribe(function () {
                block.style.overflow = 'visible';
                document.getElementById('leftblocks').style.display = 'none';
                oImg.src = img1;
                oImg.alt = "Click to show Left Blocks";
                oImg.title = "Click to show Left Blocks";
                document.cookie = 'leftblocksmode=none';
            });

            ani.animate();
        }
    }

    function setBlockStatus() {
        var blocks = getElementsByClassName('block-box-item');
        for (i in blocks) {
            if (blocks[i].id) {
                var block_id = blocks[i].id.substring(4);
                var block = document.getElementById('blk_' + block_id);
                var status = getCookie('blk_' + block_id);

                if (status != null) {
                    block.title = block.scrollHeight;
                    blocks[i].style.display = status;
                }
            }
        }
    }

    function updateBlockCollapseIcon(id, onoff) {
        var block = document.getElementById('row_' + id);
        var image = document.getElementById('img_' + id);
        var link = document.getElementById('lnk_' + id);

        if (block.style.display == '') {
            image.src = nxp_layout_url + "/images/arrow-up-" + onoff + ".gif";
        }
        else {
            image.src = nxp_layout_url + "/images/arrow-down-" + onoff + ".gif";
        }
        if (onoff == 'on') {
            link.style.color = '#FFFFFF';
        }
        else {
            link.style.color = '#D9E1F6';
        }
    }

    function toggleblock(id) {
        var block = document.getElementById('blk_' + id);
        var row = document.getElementById('row_' + id);
        var cell = document.getElementById('cell_' + id);
        var ani = new YAHOO.util.Anim(block);

        if (row.style.display == '') {
            block.title = block.scrollHeight;
            ani.attributes.height = { to: 0 };
            ani.attributes.opacity = { to: 0 };
            ani.duration = 0.3;
            //ani.method = YAHOO.util.Easing.easeInStrong;
            block.style.overflow = 'hidden';

            ani.onComplete.subscribe(function () {
                row.style.display = 'none';
                updateBlockCollapseIcon(id, 'on');
                block.style.overflow = 'visible';
            });

            document.cookie = 'blk_' + id + '=none';
            ani.animate();
        }
        else {
            var height = block.title;
            row.style.display = '';
            ani.attributes.height = { to: height };
            ani.attributes.opacity = { to: 1 };
            ani.duration = 0.3;
            //ani.method = YAHOO.util.Easing.easeOutStrong;
            block.style.overflow = 'hidden';

            ani.onComplete.subscribe(function () {
                updateBlockCollapseIcon(id, 'on');
                block.style.overflow = 'visible';
            });

            document.cookie = 'blk_' + id + '=';
            ani.animate();
        }

    }

    function getElementsByClassName(className, tag, elm){
        var testClass = new RegExp("(^|\\s)" + className + "(\\s|$)");
        var tag = tag || "*";
        var elm = elm || document;
        var elements = (tag == "*" && elm.all)? elm.all : elm.getElementsByTagName(tag);
        var returnElements = [];
        var current;
        var length = elements.length;
        for(var i=0; i<length; i++){
            current = elements[i];
            if(testClass.test(current.className)){
                returnElements.push(current);
            }
        }
        return returnElements;
    }

