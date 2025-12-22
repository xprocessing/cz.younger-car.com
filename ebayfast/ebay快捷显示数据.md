# 写一个 js 书签

1.查找页面中所有 有属性data-item-id的span ，获得的值
2. 在span的父元素的父元素中 增加链接按钮：点击查看，点击后新窗口访问 https://www.ebay.com/bin/purchaseHistory?item= + data-item-id
3.鼠标划过 按钮 在页面右侧预览页面：https://www.ebay.com/bin/purchaseHistory?item= + data-item-id