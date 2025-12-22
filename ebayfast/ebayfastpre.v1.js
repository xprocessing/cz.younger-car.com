javascript:(function() {
    // 配置项
    const domain = window.location.origin;
    let PREVIEW_URL = domain+'/bin/purchaseHistory?item=';
    const LINK_TEXT = '点击查看';

    // 1. 查找所有带有 data-item-id 属性的 span 元素
    const spans = document.querySelectorAll('span[data-item-id]');

    // 如果已存在预览框，先移除
    const oldPreview = document.getElementById('ebay-item-preview-container');
    if (oldPreview) oldPreview.remove();

    // 2. 创建预览容器
    const previewContainer = document.createElement('div');
    previewContainer.id = 'ebay-item-preview-container';
    Object.assign(previewContainer.style, {
        position: 'fixed',
        top: '0',
        right: '-60%', // 初始位置在视口外，宽度为60%
        width: '60%', // 宽度为窗口的60%
        height: '100vh', // 高度为窗口的100%
        backgroundColor: '#fff',
        borderLeft: '1px solid #ccc',
        boxShadow: '-2px 0 10px rgba(0,0,0,0.1)',
        zIndex: '9999999', // 确保在最上层
        transition: 'right 0.3s ease-in-out',
        overflow: 'hidden',
        borderRadius: '5px 0 0 5px'
    });

    // 创建加载提示
    const loadingIndicator = document.createElement('div');
    loadingIndicator.textContent = '加载中...';
    Object.assign(loadingIndicator.style, {
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        height: '100%',
        fontSize: '16px',
        color: '#666'
    });
    previewContainer.appendChild(loadingIndicator);

    // 创建关闭按钮
    const closeButton = document.createElement('button');
    closeButton.textContent = '×';
    Object.assign(closeButton.style, {
        position: 'absolute',
        top: '10px',
        right: '10px',
        backgroundColor: 'rgba(0,0,0,0.5)',
        color: 'white',
        border: 'none',
        borderRadius: '50%',
        width: '24px',
        height: '24px',
        cursor: 'pointer',
        fontSize: '16px',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: '10'
    });
    closeButton.addEventListener('click', () => {
        previewContainer.style.right = '-60%'; // 隐藏时也对应60%宽度
    });
    previewContainer.appendChild(closeButton);

    // 创建预览iframe
    const previewIframe = document.createElement('iframe');
    Object.assign(previewIframe.style, {
        width: '100%',
        height: '100%',
        border: 'none'
    });
    previewContainer.appendChild(previewIframe);

    // 将预览容器添加到页面
    document.body.appendChild(previewContainer);

    // 3. 遍历并处理每个 span 元素
    spans.forEach(span => {
        const itemId = span.getAttribute('data-item-id');
        if (!itemId) return;

        const grandParent = span.parentElement?.parentElement;
        if (!grandParent) return;

        // 检查是否已添加过链接，避免重复添加
        if (grandParent.querySelector('.ebay-item-link')) return;

        // 创建链接
        const link = document.createElement('a');
        link.href = `${PREVIEW_URL}${itemId}`;
        link.target = '_blank';
        link.textContent = LINK_TEXT;
        link.className = 'ebay-item-link'; // 添加类名用于识别

        // 链接样式
        Object.assign(link.style, {
            marginLeft: '10px',
            padding: '3px 8px',
            border: '1px solid #007bff',
            borderRadius: '4px',
            color: '#007bff',
            textDecoration: 'none',
            cursor: 'pointer',
            fontSize: '12px',
            display: 'inline-block'
        });

        // 鼠标悬停效果
        link.addEventListener('mouseenter', () => {
            previewIframe.src = `${PREVIEW_URL}${itemId}`;
            previewContainer.style.right = '0'; // 滑入预览框
            loadingIndicator.style.display = 'flex'; // 显示加载中
        });

        link.addEventListener('mouseleave', () => {
            // 延迟隐藏，防止鼠标快速移动时误触发
            setTimeout(() => {
                if (!previewContainer.matches(':hover')) {
                    previewContainer.style.right = '-60%'; // 隐藏时也对应60%宽度
                }
            }, 100);
        });

        // 预览框鼠标悬停时保持显示
        previewContainer.addEventListener('mouseenter', () => {
            previewContainer.style.right = '0';
        });

        previewContainer.addEventListener('mouseleave', () => {
            previewContainer.style.right = '-60%'; // 隐藏时也对应60%宽度
        });

        // iframe加载完成后隐藏加载提示
        previewIframe.addEventListener('load', () => {
            loadingIndicator.style.display = 'none';
        });

        grandParent.appendChild(link);
    });

    // 提示信息
    console.log(`已为 ${spans.length} 个商品添加查看链接和悬停预览功能！`);
})();