function run() {
    require('child_process').execSync("php jobs-smaller.php", {stdio: "inherit"});
}

setInterval(run, 60000);
run();