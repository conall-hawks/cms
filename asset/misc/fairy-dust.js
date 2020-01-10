function fairyDustCursor(){

    // Properties.
    //var possibleColors = ["#D61C59", "#E7D84B", "#1B8798"];
    var possibleColors = ["lime", "darkorange", "#bbb", "red", "skyblue"];
    var width = window.innerWidth;
    var height = window.innerHeight;
    var cursor = {x: width, y: height};
    var particles = [];

    function init(){
        bindEvents();
        loop();
    }

    // Bind events that are needed
    function bindEvents(){
        document.addEventListener("mousemove", onMouseMove);
        document.addEventListener("touchmove", onTouchMove);
        document.addEventListener("touchstart", onTouchMove);
        window.addEventListener("resize", onWindowResize);
    }

    // Bind events that are needed
    fairyDustCursor.unbindEvents = function(){
        document.removeEventListener("mousemove", onMouseMove);
        document.removeEventListener("touchmove", onTouchMove);
        document.removeEventListener("touchstart", onTouchMove);
        window.removeEventListener("resize", onWindowResize);
    }

    function onWindowResize(e){
        width = window.innerWidth;
        height = window.innerHeight;
    }

    function onTouchMove(e){
        if(e.touches.length > 0){
            for(var i = 0; i < e.touches.length; i++){
                addParticle(e.touches[i].clientX, e.touches[i].clientY, possibleColors[Math.floor(Math.random()*possibleColors.length)]);
            }
        }
    }

    function onMouseMove(e){
        cursor.x = e.clientX;
        cursor.y = e.clientY;

        addParticle(cursor.x, cursor.y, possibleColors[Math.floor(Math.random()*possibleColors.length)]);
    }

    function addParticle(x, y, color){
        var particle = new Particle();
        particle.init(x, y, color);
        particles.push(particle);
    }

    function updateParticles(){

        // Updated
        for(var i = 0; i < particles.length; i++){
            particles[i].update();
        }

        // Remove dead particles
        for(var i = particles.length -1; i >= 0; i--){
            if(particles[i].lifeSpan < 0){
                particles[i].die();
                particles.splice(i, 1);
            }
        }

    }

    function loop(){
        requestAnimationFrame(loop);
        updateParticles();
    }

    /**
     * Particles
     */

    function Particle(){

        this.character     = "*";
        this.lifeSpan      = 120; //ms
        this.initialStyles = {
            "display":       "block",
            "fontSize":      "16px",
            "pointerEvents": "none",
            "position":      "absolute",
            "top":           "0",
            "will-change":   "transform",
            "z-index":       "10000000"
        };

        // Init, and set properties
        this.init = function(x, y, color){

            this.velocity = {
                x: (Math.random() < 0.5 ? -1 : 1) * (Math.random() / 2),
                y: Math.random()
            };

            this.position = {x: x + window.scrollX, y: y + window.scrollY};
            this.initialStyles.color = color;

            this.element = document.createElement("span");
            this.element.innerHTML = this.character;
            applyProperties(this.element, this.initialStyles);
            this.update();

            document.body.appendChild(this.element);
        };

        this.update = function(){
            this.position.x += this.velocity.x * (Math.random() + 1);
            this.position.y += this.velocity.y * (Math.random() + 1);
            this.lifeSpan--;
            this.element.style.transform = "translate3d(" + this.position.x + "px, " + this.position.y + "px, 0) scale(" + (this.lifeSpan / 120) + ")";
        }

        this.die = function(){
            this.element.parentNode.removeChild(this.element);
        }

    }

    /**
     * Utils
     */

    // Applies css `properties` to an element.
    function applyProperties(target, properties){
        for(var key in properties){
            if(!Object.prototype.hasOwnProperty.call(properties, key)) continue;
            target.style[key] = properties[key];
        }
    }

    init();
}

// Run.
fairyDustCursor();
