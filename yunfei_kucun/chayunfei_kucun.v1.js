// ===================== 工具函数 =====================
// 获取认证token
const getAuthToken = () => (document.cookie.match(/auth-token=([^;]+)/) || [])[1] || null;

// 生成UUID（用于x-ak-request-id）
const generateUUID = () => {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
};

// 单斜杠转义处理函数
const escapeSlashes = (str) => {
    return str.replace(/\\/g, '\\\\')  // 转义反斜杠
              .replace(/"/g, '\\"')    // 转义双引号
              .replace(/'/g, "\\'")    // 转义单引号
              .replace(/\//g, '\\/');  // 转义正斜杠
};

// ===================== 配置与数据格式化 =====================
// 获取请求头配置（动态生成token和request-id）
const getHeaderConfig = () => ({
    "accept": "application/json, text/plain, */*",
    "accept-encoding": "gzip, deflate, br, zstd",
    "accept-language": "zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6",
    "ak-client-type": "web",
    "ak-origin": "https://erp.lingxing.com",
    "auth-token": getAuthToken(),
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
    "x-ak-request-id": generateUUID(), // 动态生成request-id
    "x-ak-request-source": "erp",
    "x-ak-uid": "10956565",
    "x-ak-version": "3.7.2.3.0.095",
    "x-ak-zid": "10796914"
});

// 格式化库存数据展示
const formatInventoryData = (dataList) => {
    const sortedList = dataList.sort((a, b) => b.average_age - a.average_age);
    
    let content = "库存信息（按平均库龄降序）：\n\n";
    sortedList.forEach((item, index) => {
        content += `${index + 1}. 仓库：${item.wh_name || '未知'}`;
        content += `   库存总量：${item.total || 0}`;
        content += `   平均库龄：${item.average_age || 0}天\n`;
    });
    
    return content;
};

// ===================== 库存查询功能 =====================
// 发送库存查询请求
const fetchInventory = async (skuValue) => {
    try {
        const escapedSku = escapeSlashes(skuValue);
        const seniorSearchList = `[{\"name\":\"SKU\",\"search_field\":\"sku\",\"search_value\":[\"${escapedSku}\"]}]`;
        
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
            "senior_search_list": seniorSearchList,
            "permission_uid_list": "",
            "country_code_list": "",
            "req_time_sequence": "/api/storage/lists$$7"
        };
        
        console.log('库存查询请求参数:', requestData);
        
        const response = await fetch('https://erp.lingxing.com/api/storage/lists', {
            method: 'POST',
            headers: getHeaderConfig(),
            body: JSON.stringify(requestData),
            credentials: 'include'
        });
        
        if (!response.ok) throw new Error(`请求失败：${response.status}`);
        
        const result = await response.json();
        
        if (result.data?.list?.length) {
            alert(formatInventoryData(result.data.list));
        } else {
            alert(`未找到SKU【${skuValue}】的库存数据`);
        }
        
    } catch (error) {
        console.error('库存查询失败:', error);
        alert(`查询失败：${error.message}\n请检查网络或权限`);
    }
};

// 处理SKU元素并添加库存查询按钮
const processSkuElements = () => {
    document.querySelectorAll('tr[rowid]').forEach(tr => {
        const skuSpans = tr.querySelectorAll('span');
        let targetSpan = null;
        
        for (const span of skuSpans) {
            if (span.textContent.includes('SKU')) {
                const nextSpan = span.nextElementSibling;
                if (nextSpan?.tagName === 'SPAN') {
                    targetSpan = nextSpan.querySelector('div > span');
                }
                break;
            }
        }
        
        if (targetSpan) {
            const skuValue = targetSpan.textContent.trim();
            const prevTr = tr.previousElementSibling;
            
            if (prevTr) {
                const leftElement = prevTr.querySelector('.left');
                if (leftElement && !leftElement.querySelector('.inventory-btn')) {
                    const button = document.createElement('button');
                    button.className = 'inventory-btn';
                    button.textContent = '查看库存';
                    button.style.cssText = `
                        margin-left: 8px;
                        padding: 4px 8px;
                        cursor: pointer;
                        background-color: #409eff;
                        color: white;
                        border: none;
                        border-radius: 4px;
                        font-size: 12px;
                    `;
                    
                    button.addEventListener('click', () => fetchInventory(skuValue));
                    leftElement.appendChild(button);
                }
            }
        }
    });
};

// ===================== 订单运费查询功能 =====================
// 获取订单信息并查询运费
const getOrderInfo = async (global_order_no) => {
    try {
        const url = `https://erp.lingxing.com/api/platforms/oms/order_list/detail?global_order_no=${global_order_no}&req_time_sequence=%2Fapi%2Fplatforms%2Foms%2Forder_list%2Fdetail$$1`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: getHeaderConfig(),
            credentials: 'include'
        });
        
        if (!response.ok) throw new Error(`HTTP错误：${response.status}`);
        
        const data = await response.json();
        
        // 解析订单数据
        const { logistics_info, receive_info } = data.data || {};
        const pre_fee_weight = logistics_info?.pre_fee_weight?.replace(' g', '') || '0';
        const pre_package_size = logistics_info?.pre_package_size || '0x0x0cm';
        const receiver_country_code = receive_info?.receiver_country_code || '';
        const postal_code = receive_info?.postal_code || '';
        const city = receive_info?.city || '';
        
        // 解析尺寸
        const [length, width, height] = pre_package_size.split('x').map(val => val.replace('cm', ''));
        const weight = parseFloat(pre_fee_weight) / 1000;
        
        console.log({
            pre_fee_weight: weight,
            length,
            width,
            height,
            receiver_country_code,
            postal_code,
            city
        });
        
        // 打开运费查询页面
        const freightUrl = `https://cz.younger-car.com/chayunfei.php?global_order_no=${global_order_no}&postcode=${postal_code}&weight=${weight}&length=${length}&width=${width}&height=${height}&city=${encodeURIComponent(city)}`;
        window.open(freightUrl, '_blank');
        
    } catch (err) {
        console.error('订单查询失败：', err.message);
        alert(`订单查询失败：${err.message}`);
    }
};

// 确保运费按钮存在（创建或更新）
const ensureFreightButton = (row) => {
    if (!row?.classList.contains('special-row')) return;
    
    const global_order_no = row.getAttribute('rowid');
    if (!global_order_no) return;
    
    const left = row.querySelector('.left');
    if (!left) return;
    
    let button = left.querySelector('button[data-freight-btn]');
    
    if (!button) {
        button = document.createElement('button');
        button.innerText = '查看运费';
        button.setAttribute('data-freight-btn', '');
        button.style.marginLeft = '8px';
        button.style.padding = '4px 8px';
        button.style.cursor = 'pointer';
        button.style.backgroundColor = '#67c23a';
        button.style.color = 'white';
        button.style.border = 'none';
        button.style.borderRadius = '4px';
        button.style.fontSize = '12px';
        
        button.onclick = (e) => {
            e.stopPropagation();
            getOrderInfo(global_order_no);
        };
        
        left.appendChild(button);
    }
};

// ===================== 初始化与监听 =====================
// 初始化所有功能
const initAllFunctions = () => {
    // 初始化库存查询按钮
    processSkuElements();
    
    // 初始化运费查询按钮
    document.querySelectorAll('.special-row').forEach(ensureFreightButton);
};

// DOM变化监听
const observer = new MutationObserver((mutations) => {
    let shouldProcess = false;
    
    mutations.forEach(mutation => {
        if (mutation.addedNodes.length) shouldProcess = true;
    });
    
    if (shouldProcess) {
        processSkuElements();
    }
});

// 事件委托：鼠标滑过订单行时确保运费按钮存在
document.addEventListener('mouseenter', (e) => {
    const row = e.target.closest('.special-row');
    if (row) ensureFreightButton(row);
}, true);

// 启动初始化
initAllFunctions();

// 监听DOM变化（处理动态加载内容）
observer.observe(document.body, { childList: true, subtree: true });

console.log('功能已初始化完成');