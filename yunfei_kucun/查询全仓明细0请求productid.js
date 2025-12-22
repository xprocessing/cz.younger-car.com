// 完整的请求配置（已替换真实接口URL）
const searchValue = "NI-19CHARGER-FL3-RD"; // 需要查询的SKU编号
// 
const requestProductIdUrl = "https://gw.lingxingerp.com/universal/storage/api/universal/summaryForsku";

const requestProductIdBody = {
  "attributes": [],
  "principalList": [],
  "productUids": [], "searchField": "sku",
  "searchValue": searchValue,
  "pageNo": 1, "pageSize": 20,
  "seniorSearchList": [],
  "req_time_sequence": "/universal/storage/api/universal/summaryForsku$$3"
};

const requestHeaders = {
  "accept": "application/json, text/plain, */*",
  "accept-encoding": "gzip, deflate, br, zstd",
  "accept-language": "zh-CN,zh;q=0.9",
  "ak-client-type": "web",
  "ak-origin": "https://erp.lingxing.com",
  "auth-token": "ad7aMY52pz/XXjODvqjXfPpV8Rfz5ZQMy2ITACOCb35IWZrtJC9o27ou5vh5CIPSVWVMXrw7qTMY15YREALYIVrZmZM2PhLiGfMBK+UXaowVldiYYcr8eb1ONz8dQXBIHvHg7XZbUh/VQmZ1VuLeZWka6AuVePDf0Ta8NgE",
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
  "x-ak-platform": "2",
  "x-ak-request-id": "00a57ece-0e70-4357-a6f1-a82f99339b50",
  "x-ak-request-source": "erp",
  "x-ak-uid": "10956565",
  "x-ak-version": "3.7.3.3.0.027",
  "x-ak-zid": "10796914"
};

// 封装为异步函数（更易复用和调试）
async function sendProductIdRequest() {
  try {
    const response = await fetch(requestProductIdUrl, {
      method: "POST",
      headers: requestHeaders,
      body: JSON.stringify(requestProductIdBody),
      credentials: "include", // 跨域携带Cookie（灵犀ERP需鉴权）
      mode: "cors" // 显式指定跨域模式
    });

    // 处理非2xx状态码
    if (!response.ok) {
      // 尝试解析错误响应体（部分接口会返回JSON格式的错误信息）
      let errorData;
      try {
        errorData = await response.json();
      } catch (e) {
        errorData = await response.text(); // 解析失败则取文本
      }
      throw new Error(`请求失败 [${response.status}]：${JSON.stringify(errorData)}`);
    }

    // 解析成功响应
    const data = await response.json();
    console.log("✅ 请求成功，响应数据：", data);   
    console.log("获取到的productId:", data.data.data[0].productId);
    return data; // 返回数据供后续使用

  } catch (error) {
    console.error("❌ 请求异常：", error.message);
    // 可根据业务需求添加错误重试、提示用户等逻辑
    throw error; // 抛出错误供上层处理
  }
}

// 执行请求
sendProductIdRequest();