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

// 格式化展示数据（表格形式）
const formatInventoryData = (dataList) => {
    // 按average_age降序排序
    const sortedList = dataList.sort((a, b) => b.average_age - a.average_age);
    
    // 创建表格样式的输出
    let content = "库存信息（按平均库龄降序）：\n\n";
    
    // 表格头部
    content += "+-----+------------------------+------------+------------+\n";
    content += "| 序号 | 仓库名称                | 库存总量   | 平均库龄   |\n";
    content += "+-----+------------------------+------------+------------+\n";
    
    // 表格内容
    sortedList.forEach((item, index) => {
        const whName = (item.wh_name || '未知').padEnd(22, ' ').substring(0, 22);
        const total = (item.total || 0).toString().padStart(10, ' ');
        const avgAge = `${item.average_age || 0}天`.padStart(10, ' ');
        
        content += `| ${(index + 1).toString().padStart(3, ' ')} | ${whName} | ${total} | ${avgAge} |\n`;
        content += "+-----+------------------------+------------+------------+\n";
    });
    
    return content;
};

// 如果需要更美观的HTML表格展示（用于自定义弹窗）
const showInventoryTable = (dataList) => {
    // 按average_age降序排序
    const sortedList = dataList.sort((a, b) => b.average_age - a.average_age);
    
    // 创建自定义弹窗
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = '50%';
    modal.style.left = '50%';
    modal.style.transform = 'translate(-50%, -50%)';
    modal.style.backgroundColor = 'white';
    modal.style.padding = '20px';
    modal.style.borderRadius = '8px';
    modal.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
    modal.style.zIndex = '9999';
    modal.style.maxHeight = '80vh';
    modal.style.overflow = 'auto';
    
    // 创建标题
    const title = document.createElement('h3');
    title.textContent = '库存信息（按平均库龄降序）';
    title.style.margin = '0 0 15px 0';
    title.style.color = '#333';
    
    // 创建表格
    const table = document.createElement('table');
    table.style.width = '100%';
    table.style.borderCollapse = 'collapse';
    table.style.minWidth = '500px';
    
    // 表头
    const thead = document.createElement('thead');
    thead.innerHTML = `
        <tr style="background-color: #f5f7fa;">
            <th style="padding: 10px; text-align: center; border: 1px solid #e6e6e6; font-weight: 600;">序号</th>
            <th style="padding: 10px; text-align: left; border: 1px solid #e6e6e6; font-weight: 600;">仓库名称</th>
            <th style="padding: 10px; text-align: center; border: 1px solid #e6e6e6; font-weight: 600;">库存总量</th>
            <th style="padding: 10px; text-align: center; border: 1px solid #e6e6e6; font-weight: 600;">平均库龄</th>
        </tr>
    `;
    
    // 表格内容
    const tbody = document.createElement('tbody');
    sortedList.forEach((item, index) => {
        const row = document.createElement('tr');
        row.style.backgroundColor = index % 2 === 0 ? '#fff' : '#fafafa';
        
        row.innerHTML = `
            <td style="padding: 8px 10px; text-align: center; border: 1px solid #e6e6e6;">${index + 1}</td>
            <td style="padding: 8px 10px; text-align: left; border: 1px solid #e6e6e6;">${item.wh_name || '未知'}</td>
            <td style="padding: 8px 10px; text-align: center; border: 1px solid #e6e6e6;">${item.total || 0}</td>
            <td style="padding: 8px 10px; text-align: center; border: 1px solid #e6e6e6;">${item.average_age || 0}天</td>
        `;
        
        tbody.appendChild(row);
    });
    
    // 创建关闭按钮
    const closeBtn = document.createElement('button');
    closeBtn.textContent = '关闭';
    closeBtn.style.marginTop = '15px';
    closeBtn.style.padding = '8px 16px';
    closeBtn.style.backgroundColor = '#409eff';
    closeBtn.style.color = 'white';
    closeBtn.style.border = 'none';
    closeBtn.style.borderRadius = '4px';
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.fontSize = '14px';
    closeBtn.onclick = () => document.body.removeChild(modal);
    
    // 创建遮罩层
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    overlay.style.zIndex = '9998';
    overlay.onclick = () => document.body.removeChild(modal) && document.body.removeChild(overlay);
    
    // 组装弹窗
    table.appendChild(thead);
    table.appendChild(tbody);
    modal.appendChild(title);
    modal.appendChild(table);
    modal.appendChild(closeBtn);
    
    // 添加到页面
    document.body.appendChild(overlay);
    document.body.appendChild(modal);
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
        const requestData = {
            "wid_list": "",
            "mid_list": "",
            "sid_list": "",
            "cid_list": "",
            "bid_list": "",
            "principal_list": "",
            "product_type_list": "",
            "product_attribute": "",
            "product_status": "",
            "search_field": "sku",
            "search_value": skuValue,
            "is_sku_merge_show": 0,
            "is_hide_zero_stock": 0,
            "offset": 0,
            "length": 20,
            "sort_field": "",
            "sort_type": "",
            "gtag_ids": "",
            "senior_search_list": seniorSearchList, // 使用手动转义的字符串
            "permission_uid_list": "",
            "country_code_list": "",
            "req_time_sequence": "/api/storage/lists$$7"
        };
        
        console.log('请求参数:', requestData);
        
        // 发送POST请求
        const response = await fetch('https://erp.lingxing.com/api/storage/lists', {
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
        if (result.data && result.data.list && result.data.list.length > 0) {
            // 使用美观的HTML表格弹窗展示
            showInventoryTable(result.data.list);
            
            // 如果需要保留alert方式的表格，可以使用：
            // const formattedContent = formatInventoryData(result.data.list);
            // alert(formattedContent);
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
                        button.addEventListener('click', function() {
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