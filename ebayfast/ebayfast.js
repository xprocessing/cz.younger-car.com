javascript:(function(){
    // 查找所有带有 data-item-id 属性的 span 元素
    const spans = document.querySelectorAll('span[data-item-id]');

    // 遍历每个 span 元素
    spans.forEach(span => {
        // 获取 data-item-id 的值
        const itemId = span.getAttribute('data-item-id');
        if (!itemId) return; // 如果没有该属性值，则跳过

        // 获取祖父级元素
        const grandParent = span.parentElement?.parentElement;
        if (!grandParent) return; // 如果祖父级元素不存在，则跳过

        // 创建链接元素
        const link = document.createElement('a');
        link.href = `https://www.ebay.com/bin/purchaseHistory?item=${itemId}`;
        link.target = '_blank'; // 新窗口打开
        link.textContent = '点击查看'; // 链接文本

        // 设置链接样式（可选，根据页面样式调整）
        link.style.marginLeft = '10px';
        link.style.padding = '2px 8px';
        link.style.border = '1px solid #007bff';
        link.style.borderRadius = '4px';
        link.style.color = '#007bff';
        link.style.textDecoration = 'none';
        link.style.cursor = 'pointer';

        // 鼠标悬停效果（可选）
        link.addEventListener('mouseover', () => {
            link.style.backgroundColor = '#007bff';
            link.style.color = '#fff';
        });
        link.addEventListener('mouseout', () => {
            link.style.backgroundColor = 'transparent';
            link.style.color = '#007bff';
        });

        // 将链接添加到祖父级元素的末尾
        grandParent.appendChild(link);
    });

    // 提示操作完成
    console.log(`已为 ${spans.length} 个商品添加查看链接！`);
})();