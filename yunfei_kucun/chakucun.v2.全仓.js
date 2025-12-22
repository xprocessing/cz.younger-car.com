// 获取认证token
const getAuthToken = () => (document.cookie.match(/auth-token=([^;]+)/) || [])[1] || null;
const auth_token = getAuthToken();

// 请求头配置
const header_data = {
    "accept": "application/json, text/plain, */*",
    "accept-encoding": "gzip, deflate, br, zstd",
    "accept-language": "zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6",
    "ak-client-type": "web",
    "ak-origin": "https://erp.lingxing.com",
    "auth-token": auth_token,
    "content-type": "application/json;charset=UTF-8",
    "origin": "https://erp.lingxing.com",
    "priority": "u=1, i",
    "referer": "https://erp.lingxing.com/",
    "sec-ch-ua": "\"Chromium\";v=\"135\", \"Not-A.Brand\";v=\"8\"",
    "sec-ch-ua-mobile": "?0",
    "sec-ch-ua-platform": "\"Windows\"",
    "sec-fetch-dest": "empty",
    "sec-fetch-mode": "cors",
    "sec-fetch-site": "cross-site",
    "sec-fetch-storage-access": "active",
    "user-agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36",
    "x-ak-company-id": "901571037510692352",
    "x-ak-env-key": "SAAS-126",
    "x-ak-language": "zh",
    "x-ak-platform": "1",
    "x-ak-request-id": "357c8014-0c07-4728-be2b-82b18f9e4a4c",
    "x-ak-request-source": "erp",
    "x-ak-uid": "10956565",
    "x-ak-version": "3.7.2.3.0.095",
    "x-ak-zid": "10796914"
};

// 格式化展示数据
const formatInventoryData = (dataList) => {
    // 按average_age降序排序
    const sortedList = dataList.sort((a, b) => b.average_age - a.average_age);

    // 格式化输出内容
    let content = "库存信息（按平均库龄降序）：\n\n";
    sortedList.forEach((item, index) => {
        content += `${index + 1}. 仓库：${item.wh_name || '未知'}`;
        content += `   库存总量：${item.total || 0}`;
        content += `   平均库龄：${item.average_age || 0}天\n`;
    });

    return content;
};

// 单斜杠转义处理函数
const escapeSlashes = (str) => {
    // 转义单斜杠为双斜杠，同时处理其他特殊字符
    return str.replace(/\\/g, '\\\\')  // 转义反斜杠
        .replace(/"/g, '\\"')    // 转义双引号
        .replace(/'/g, "\\'")    // 转义单引号
        .replace(/\//g, '\\/');  // 转义正斜杠
};

// 发送库存查询请求
const fetchInventory = async (skuValue) => {
    try {
        // 对SKU值进行单斜杠转义处理
        const escapedSku = escapeSlashes(skuValue);

        // 构建高级搜索列表（手动转义方式）
        const seniorSearchList = `[{\"name\":\"SKU\",\"search_field\":\"sku\",\"search_value\":[\"${escapedSku}\"]}]`;

        // 构建请求参数
        const requestData ={"productId":83504,"firstId":"1995641584354136071","hasChildrenRow":false,"attributes":[],"principalList":[],"productUids":[],"searchField":"sku","searchValue": skuValue ,"pageNo":1,"pageSize":20,"seniorSearchList":[],"req_time_sequence":"/universal/storage/api/universal/summaryForsku/detail$$2"};

        console.log('请求参数:', requestData);

        // 发送POST请求
        const response = await fetch('https://gw.lingxingerp.com/universal/storage/api/universal/summaryForsku/detail', {
            method: 'POST',
            headers: header_data,
            body: JSON.stringify(requestData),
            credentials: 'include'
        });

        if (!response.ok) {
            throw new Error(`请求失败：${response.status}`);
        }

        const result = await response.json();

        // 处理返回数据
        if (result.data && result.data.childrens && result.data.childrens.length > 0) {
            const formattedContent = formatInventoryData(result.data.childrens);
            alert(formattedContent);
        } else {
            alert(`未找到SKU【${skuValue}】的库存数据`);
        }

    } catch (error) {
        console.error('库存查询失败:', error);
        alert(`查询失败：${error.message}\n请检查网络或权限`);
    }
};

// 主处理逻辑
const processElements = () => {
    // 获取所有含rowid属性的tr标签
    const trList = document.querySelectorAll('tr[rowid]');

    trList.forEach(tr => {
        // 查找内容含SKU的span
        const skuSpans = tr.querySelectorAll('span');
        let targetSpan = null;

        for (const span of skuSpans) {
            if (span.textContent.includes('SKU')) {
                const nextSpan = span.nextElementSibling;
                if (nextSpan && nextSpan.tagName === 'SPAN') {
                    targetSpan = nextSpan.querySelector('div > span');
                }
                break;
            }
        }

        if (targetSpan) {
            const skuValue = targetSpan.textContent.trim();
            console.log('SKU对应的值:', skuValue);

            // 找到上一个tr的.left元素，添加按钮
            const prevTr = tr.previousElementSibling;
            if (prevTr) {
                const leftElement = prevTr.querySelector('.left');
                if (leftElement) {
                    // 避免重复添加按钮
                    if (!leftElement.querySelector('.inventory-btn')) {
                        const button = document.createElement('button');
                        button.className = 'inventory-btn';
                        button.textContent = '查看库存';
                        button.style.marginLeft = '8px';
                        button.style.padding = '4px 8px';
                        button.style.cursor = 'pointer';
                        button.style.backgroundColor = '#409eff';
                        button.style.color = 'white';
                        button.style.border = 'none';
                        button.style.borderRadius = '4px';
                        button.style.fontSize = '12px';

                        // 绑定点击事件
                        button.addEventListener('click', function () {
                            fetchInventory(skuValue);
                        });

                        leftElement.appendChild(button); // 添加到.left内部
                    }
                }
            }
        }
    });
};

// 执行处理
processElements();

// 如果页面是动态加载的，可以添加监听
// 例如监听DOM变化，自动重新处理
const observer = new MutationObserver((mutations) => {
    let shouldProcess = false;
    mutations.forEach(mutation => {
        if (mutation.addedNodes.length) {
            shouldProcess = true;
        }
    });
    if (shouldProcess) {
        processElements();
    }
});

observer.observe(document.body, { childList: true, subtree: true });