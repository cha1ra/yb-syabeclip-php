export function draw(clips, currentTranscriptIndex, title, subTitle) {
    const { transcript, zoom = 1, title:isTitle = false } = clips.value[currentTranscriptIndex.value];
    const video = document.getElementById('videoElement');
    const canvas = document.getElementById('videoCanvas');
    const context = canvas.getContext('2d');

    // 動画をズームして描画
    const videoWidth = video.videoWidth;
    const videoHeight = video.videoHeight;
    const zoomedWidth = videoWidth / zoom;
    const zoomedHeight = videoHeight / zoom;
    const offsetX = (videoWidth - zoomedWidth) / 2;
    const offsetY = (videoHeight - zoomedHeight) / 2;
    context.drawImage(video, offsetX, offsetY, zoomedWidth, zoomedHeight, 0, 0, canvas.width, canvas.height);

    const padding = 32;
    const fontSize = 64;
    const lines = transcript.split('\n');
    const lineHeight = fontSize + padding;
    const textHeight = lineHeight * lines.length;
    context.font = `${fontSize}px 'M PLUS 1p'`;
    context.fillStyle = "rgba(0, 0, 0, 0.5)"; // 黒透過背景
    const yPosition = canvas.height * (3 / 4); // 下から1/4の位置
    context.fillRect(0, yPosition - textHeight / 2, canvas.width, textHeight); // 背景の幅はcanvas幅いっぱいに
    context.fillStyle = "white";
    context.textAlign = "center"; // 文字を中央寄せ
    context.textBaseline = "middle"; // 文字の垂直方向を中央寄せ
    // 改行文字で分割して描画
    lines.forEach((line, index) => {
        context.fillText(line, canvas.width / 2, yPosition + index * lineHeight - (lines.length - 1) * lineHeight / 2);
    });



    // 角丸の矩形を描画する関数
    function drawRoundedRect(ctx, x, y, width, height, radius) {
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
    }

    // isTitleがtrueの場合は、中央に角丸長方形を描画し、その中にタイトルを描画
    if (isTitle) {
        const titleRectWidth = canvas.width * 0.8; // キャンバス幅の80%
        const titleRectHeight = 200; // 高さを増やす
        const titleRectX = (canvas.width - titleRectWidth) / 2;
        const titleRectY = (canvas.height - titleRectHeight) / 2;
        
        // 角丸長方形を描画
        context.fillStyle = 'rgba(0, 0, 0, 0.7)';
        drawRoundedRect(context, titleRectX, titleRectY, titleRectWidth, titleRectHeight, 20);
        context.fill();
        
        // 影の設定
        context.shadowColor = 'rgba(0, 0, 0, 0.5)';
        context.shadowBlur = 5;
        context.shadowOffsetX = 3;
        context.shadowOffsetY = 3;
        
        // タイトルテキストを描画
        context.font = 'bold 48px "Noto Serif JP"';
        context.fillStyle = 'white';
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        context.fillText(title.value, canvas.width / 2, canvas.height / 2 - 45);

        // サブタイトルテキストを描画
        context.font = 'bold 64px "Noto Serif JP"';
        context.fillText(subTitle.value, canvas.width / 2, canvas.height / 2 + 45);
        
        // 影をリセット
        context.shadowColor = 'transparent';
        context.shadowBlur = 0;
        context.shadowOffsetX = 0;
        context.shadowOffsetY = 0;
    }
    // タイトルじゃない場合、左上にタイトルを描画
    if(!isTitle){
        // 左上にタイトルテロップを描画
        const titleText = title.value || "";
        const titleFontSize = 36;
        const tiltlePadding = 8;

        const subTitleText = subTitle.value || "";
        const subTitleFontSize = 48;
        
        context.imageSmoothingEnabled = true; // フォントをスムーズにする
        context.font = `${subTitleFontSize}px 'M PLUS 1p'`;
        const subTitleTextWidth = context.measureText(subTitleText).width;
        const rectWidth = subTitleTextWidth + padding * 2;
        const rectHeight = subTitleFontSize + padding * 2;
        const rectX = padding;
        const rectY = 200;

        // 影をつける
        context.shadowColor = "rgba(0, 0, 0, 0.5)";
        context.shadowBlur = 10;
        context.shadowOffsetX = 5;
        context.shadowOffsetY = 5;

        context.textAlign = "left"; // 文字を左寄せ
        context.textBaseline = "top"; // 文字の垂直方向を中央寄せ

        context.font = `${titleFontSize}px 'M PLUS 1p'`;
        const titleTextWidth = context.measureText(titleText).width;
        const titleRectWidth = titleTextWidth + tiltlePadding * 2;
        const titleRectHeight = titleFontSize + tiltlePadding * 2;

        // タイトルを描画
        context.font = `${titleFontSize}px 'M PLUS 1p'`;
        context.fillStyle = "black"; // 黒背景
        context.fillRect(rectX + padding - tiltlePadding, rectY - titleFontSize - tiltlePadding * 3, titleRectWidth, titleRectHeight);
        context.fillStyle = "white";
        context.fillText(titleText, rectX + padding, rectY - titleFontSize - tiltlePadding * 2); 

        // 角丸の矩形を描画
        const radius = 20; // 角丸の半径
        context.fillStyle = "rgba(255, 255, 255, 1)"; // 白背景
        drawRoundedRect(context, rectX, rectY, rectWidth, rectHeight, radius);
        context.fill();
        context.font = `${subTitleFontSize}px 'M PLUS 1p'`;
        context.shadowColor = "transparent"; // 影をリセット
        context.fillStyle = "black";
        context.fillText(subTitleText, rectX + padding, rectY + padding); 
    }


    requestAnimationFrame(() => draw(clips, currentTranscriptIndex, title, subTitle));
}