# Netis-MW5360-Code-Injection


Netis Router Exploit PoC
Overview

This project contains a Proof of Concept (PoC) for exploiting a vulnerability in Netis routers. The exploit allows an attacker to remotely execute commands on a vulnerable device. This is achieved by sending specially crafted HTTP POST requests to a target router's web interface.
Vulnerability

The Netis router exploit takes advantage of an insecure endpoint /cgi-bin/skk_set.cgi that improperly handles input, allowing an attacker to inject and execute commands remotely. This PoC uses the base64_encode technique to send commands to the router for remote code execution (RCE).
PoC Details:

    The code allows you to check if a target router is vulnerable, execute remote commands, and exploit the device using HTTP POST requests.
    The router is considered vulnerable if it runs a specific model and version of the Netis router firmware.
    The command stager uses the /cgi-bin/skk_set.cgi endpoint to execute commands.

How to Use the Exploit
Prerequisites:

    Ensure you have PHP installed on your machine.
    A vulnerable Netis router on your network or remotely accessible.

Step-by-Step Usage:

    Clone the Repository:

    bash

git clone https://github.com/yourusername/netis-exploit-poc.git
cd netis-exploit-poc

Set the Target:

    Open the poc.php file.
    On line 67, replace your_target_ip with the actual IP address of the target router.

php

$url = "http://your_target_ip" . $this->targetUri . $uri;

Run the Exploit:

    Run the script using the following command in your terminal:

    bash

    php poc.php

Check Vulnerability:

    The script will first check if the target router is vulnerable by querying the /cgi-bin/skk_get.cgi endpoint.
    If the target is vulnerable, the exploit process will continue.

Execute Commands:

    The script will send remote commands to the router, allowing you to execute arbitrary commands using the router’s web interface.
