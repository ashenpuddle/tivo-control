# tivo-control
PHP code that sends telnet commands to a TiVo box

Basically, what we have here is a Rube Goldberg machine that allows Google Home voice commands to control a TiVo box. So what are the components of the machine? Here's the chain:

Voice command to Google Home --> IFTTT Applet --> Apache on Raspberry Pi --> TiVo

Yeah, I know...but it is COOL.
