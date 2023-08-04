<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Extract token - Pasterino</title>
</head>
<body>
  <div class="outcome"></div>
  <script>
    const outcome = document.currentScript.previousElementSibling;
    async function main() {
      const payload = new Map(
        window.location.hash
          .substring(1)
          .split("&")
          .map(pair => pair.split("="))
      );
      
      if (payload.get("state") !== localStorage.getItem("s")) {
        alert("Cross-Site Request Forgery detected!");
        console.log(payload.get("status"), "!==", localStorage.getItem("s"));
        return;
      }
      
      if (payload.has("error")) {
        outcome.textContent = decodeURIComponent(payload.get("error_description") ?? encodeURIComponent("Unknown error"));
        outcome.style.color = "crimson";
        return;
      }
      
      const r = await fetch("/pasterino-server/create-user", {
        method: "post",
        body: JSON.stringify({
          state: payload.get("state"),
          access_token: payload.get("access_token")
        })
      });
      
      if (r.ok) {
        const url = new URL("http:/localhost/pasterino-server/access");
        url.searchParams.set("s", localStorage.getItem("s") ?? "");
        window.location.replace(url);
      }
    }
    
    window.onload = main;
  </script>
</body>
</html>