<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Get cookie - Pasterino</title>
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    html {
      background-color: black;
    }
    
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      
      width: 100vw;
      height: 100vh;
      overflow: hidden;
    }
    
    div {
      padding: 16px;
      background-color: dodgerblue;
      border-radius: 10px;
      color: white;
    }
  </style>
</head>
<body>
  <button>Connect With Twitch!</button>
  <script>
    const url = new URL("https://id.twitch.tv/oauth2/authorize");
    url.searchParams.set("response_type", "token");
    url.searchParams.set("client_id", "drped831h9u9gncdtb8qx3exvqvxou");
    url.searchParams.set("redirect_uri", "http://localhost/pasterino-server/auth");
    url.searchParams.set("scope", "user_read");
    
    const button = document.currentScript.previousElementSibling;
    button.addEventListener("click", async () => {
      const response = await fetch("http://localhost/pasterino-server/gen-code");
      
      if (!response.ok) {
        alert("Could not get state.");
        return;
      }
      
      const state = await response.text();
      
      localStorage.setItem("s", state);
  
      url.searchParams.set("state", state);
      
      window.location.replace(url);
    });
  </script>
</body>
</html>