javascript: (function () {
    // 配置项
    const domain = window.location.origin;
    const PREVIEW_URL_SALE = domain + '/bin/purchaseHistory?item=';
    const PREVIEW_URL_PUBLISH = domain + '/rvh/';
    const LINK_TEXT_SALE = '查看销售';
    const LINK_TEXT_PUBLISH = '查看发布记录';

    // 1. 查找所有带有 data-item-id 属性的 span 元素
    const spans = document.querySelectorAll('.static-table span[data-item-id]');

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

    // 通用创建链接函数
    function createLink(text, url, color, borderColor) {
        const link = document.createElement('a');
        link.href = url;
        link.target = '_blank';
        link.textContent = text;
        link.className = `ebay-item-link ${text.replace(/\s+/g, '-').toLowerCase()}`;

        Object.assign(link.style, {
            marginLeft: '5px',
            padding: '3px 8px',
            border: `1px solid ${borderColor}`,
            borderRadius: '4px',
            color: color,
            textDecoration: 'none',
            cursor: 'pointer',
            fontSize: '12px',
            display: 'inline-block'
        });

        return link;
    }

    // 通用绑定事件函数
    function bindLinkEvents(link, url) {
        link.addEventListener('mouseenter', () => {
            previewIframe.src = url;
            previewContainer.style.right = '0';
            loadingIndicator.style.display = 'flex';
        });

        link.addEventListener('mouseleave', () => {
            setTimeout(() => {
                if (!previewContainer.matches(':hover')) {
                    previewContainer.style.right = '-60%';
                }
            }, 100);
        });
    }

    // 绑定预览容器事件
    function bindContainerEvents() {
        previewContainer.addEventListener('mouseenter', () => {
            previewContainer.style.right = '0';
        });

        previewContainer.addEventListener('mouseleave', () => {
            previewContainer.style.right = '-60%';
        });

        previewIframe.addEventListener('load', () => {
            loadingIndicator.style.display = 'none';
        });
    }

    bindContainerEvents();

    ///针对搜索列表页增加预览功能///
    // 3. 遍历并处理每个 span 元素
    spans.forEach(span => {
        const itemId = span.getAttribute('data-item-id');
        if (!itemId) return;

        const grandParent = span.parentElement?.parentElement;
        if (!grandParent) return;

        // 检查是否已添加过链接，避免重复添加
        if (grandParent.querySelector('.ebay-item-link')) return;

        // 创建查看销售链接
        const saleLink = createLink(LINK_TEXT_SALE, `${PREVIEW_URL_SALE}${itemId}`, '#007bff', '#007bff');
        bindLinkEvents(saleLink, `${PREVIEW_URL_SALE}${itemId}`);
        grandParent.appendChild(saleLink);

        // 创建查看发布记录链接
        const publishLink = createLink(LINK_TEXT_PUBLISH, `${PREVIEW_URL_PUBLISH}${itemId}`, '#28a745', '#28a745');
        bindLinkEvents(publishLink, `${PREVIEW_URL_PUBLISH}${itemId}`);
        grandParent.appendChild(publishLink);
    });

    //// 增加对列表页面的支持dzhttps://cn.ebay.com/b/Dirt-Oval-Racing-Parts/175578/bn_583892
    const items2 = document.querySelectorAll('.brwrvr__item-results a[data-interactions]');
    items2.forEach(item => {
        const itemId = item.href.match(/\/itm\/(\d+)\?/) ? item.href.match(/\/itm\/(\d+)\?/)[1] : null;
        if (!itemId) return;

        const grandParent = item.parentElement?.parentElement;
        if (!grandParent) return;

        if (grandParent.querySelector('.ebay-item-link')) return;

        // 创建查看销售链接
        const saleLink = createLink(LINK_TEXT_SALE, `${PREVIEW_URL_SALE}${itemId}`, '#007bff', '#007bff');
        bindLinkEvents(saleLink, `${PREVIEW_URL_SALE}${itemId}`);
        grandParent.appendChild(saleLink);

        // 创建查看发布记录链接
        const publishLink = createLink(LINK_TEXT_PUBLISH, `${PREVIEW_URL_PUBLISH}${itemId}`, '#28a745', '#28a745');
        bindLinkEvents(publishLink, `${PREVIEW_URL_PUBLISH}${itemId}`);
        grandParent.appendChild(publishLink);
    });

    ///增加对搜索页面的支持
    const items3 = document.querySelectorAll('.srp-results .su-card-container__content a[data-interactions]');
    items3.forEach(item => {
        const itemId = item.href.match(/\/itm\/(\d+)\?/) ? item.href.match(/\/itm\/(\d+)\?/)[1] : null;
        if (!itemId) return;

        const grandParent = item.parentElement;
        if (!grandParent) return;

        if (grandParent.querySelector('.ebay-item-link')) return;

        // 创建查看销售链接
        const saleLink = createLink(LINK_TEXT_SALE, `${PREVIEW_URL_SALE}${itemId}`, '#f51010ff', '#007bff');
        bindLinkEvents(saleLink, `${PREVIEW_URL_SALE}${itemId}`);
        grandParent.appendChild(saleLink);

        // 创建查看发布记录链接
        const publishLink = createLink(LINK_TEXT_PUBLISH, `${PREVIEW_URL_PUBLISH}${itemId}`, '#28a745', '#28a745');
        bindLinkEvents(publishLink, `${PREVIEW_URL_PUBLISH}${itemId}`);
        grandParent.appendChild(publishLink);
    });



    ///增加详情页面的推荐列表的支持
    const items4 = document.querySelectorAll('a[data-track]');
    items4.forEach(item => {
        const itemId = item.href.match(/\/itm\/(\d+)\?/) ? item.href.match(/\/itm\/(\d+)\?/)[1] : null;
        if (!itemId) return;

        const grandParent = item.parentElement.parentElement;
        if (!grandParent) return;

        if (grandParent.querySelector('.ebay-item-link')) return;

        // 创建查看销售链接
        const saleLink = createLink(LINK_TEXT_SALE, `${PREVIEW_URL_SALE}${itemId}`, '#f51010ff', '#007bff');
        bindLinkEvents(saleLink, `${PREVIEW_URL_SALE}${itemId}`);
        grandParent.appendChild(saleLink);

        // 创建查看发布记录链接
        const publishLink = createLink(LINK_TEXT_PUBLISH, `${PREVIEW_URL_PUBLISH}${itemId}`, '#28a745', '#28a745');
        bindLinkEvents(publishLink, `${PREVIEW_URL_PUBLISH}${itemId}`);
        grandParent.appendChild(publishLink);
    });

    ///增加对详情页本产品的支持
     const items5 = document.querySelectorAll('.x-item-title__mainTitle');
    items5.forEach(item => {
        //js获取网页网址
        const thisUrl = window.location.href;        

        const itemId = thisUrl.match(/\/itm\/(\d+)\?/) ? thisUrl.match(/\/itm\/(\d+)\?/)[1] : null;
        if (!itemId) return;

        const grandParent = item;
        if (!grandParent) return;

        if (grandParent.querySelector('.ebay-item-link')) return;

        // 创建查看销售链接
        const saleLink = createLink(LINK_TEXT_SALE, `${PREVIEW_URL_SALE}${itemId}`, '#f51010ff', '#007bff');
        bindLinkEvents(saleLink, `${PREVIEW_URL_SALE}${itemId}`);
        grandParent.appendChild(saleLink);

        // 创建查看发布记录链接
        const publishLink = createLink(LINK_TEXT_PUBLISH, `${PREVIEW_URL_PUBLISH}${itemId}`, '#28a745', '#28a745');
        bindLinkEvents(publishLink, `${PREVIEW_URL_PUBLISH}${itemId}`);
        grandParent.appendChild(publishLink);
    });






    // 提示信息
    const totalItems = spans.length + items2.length + items3.length+items4.length;
    console.log(`已为 ${totalItems} 个商品添加查看链接和悬停预览功能！`);
})();