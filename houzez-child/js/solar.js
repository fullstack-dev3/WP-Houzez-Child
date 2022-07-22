// Common Trig functions
var cos = Math.cos;
var sin = Math.sin;
var pi = Math.PI;
var sqrt = Math.sqrt;
var atan2 = Math.atan2;
var acos = Math.acos;

// 3D rotation matricies
// See: https://en.wikipedia.org/wiki/Rotation_matrix
function rotateX(t) {
    return [[1, 0, 0], [0, cos(t), -sin(t)], [0, sin(t), cos(t)]];
}

function rotateY(t) {
    return [[cos(t), 0, sin(t)], [0, 1, 0], [-sin(t), 0, cos(t)]];
}

function rotateZ(t) {
    return [[cos(t), -sin(t), 0], [sin(t), cos(t), 0], [0, 0, 1]];
}

// Unit vectors
var up = [[1], [0], [0]];
var north = [[0],[0],[1]];
var east = [[0], [1], [0]];

// Display degrees

function toDeg(radians) {
    var deg = radians / pi * 180;
    return Math.round(deg * 100) / 100.0;
}

// -------------------------------------------
// Astronomical constants
// -------------------------------------------

// See: http://www.astrophysicsspectator.com/tables/Earth.html
// See: http://www.timeanddate.com/astronomy/perihelion-aphelion-solstice.html (for perihelion)
// See: http://www.timeanddate.com/calendar/december-solstice.html (for solstice)
// See: http://www.esrl.noaa.gov/gmd/grad/solcalc/ (for solstice_longitude, found by trial and error to be 107.2E)

// Orbit
var sidereal_rotation = 86164.09054;       // seconds
var sidereal_orbit = 365.242190;           // days
var axial_tilt = 23.44;                    // degrees

// Summer Solstice (Southern Hemisphere)
var summer_solstice = Date.UTC(2015, 11, 22, 4, 49, 0); // Solstice
var solstice_longitude = 107.2 // At 107.2E, Solar noon coincides with the summer solstice

// Eccentricity of orbit
var orbit_eccentricity = 0.01671022;
var perihelion = Date.UTC(2016, 0, 2, 22, 49, 0);
var solstice_to_perihelion = (perihelion - summer_solstice) / 1000; // seconds between solstice and perihelion

// -------------------------------------------
// Kepler's equations
// -------------------------------------------

// Calculate eccentric anomaly (E) from eccentricity (e) and mean anomaly (M)
// See: http://www.jgiesen.de/kepler/kepler.html
// See: https://en.wikipedia.org/wiki/True_anomaly
function E(e, M) {
    // Solve Kepler's equations with 5 iterations of Newton's method
    // 0 = f(E) = E - e * sin(E) - M
    function f(E) {
        return E - e * sin(E) - M;
    }
    function df(E) { // first derivative of f(E)
        return 1 - e * cos(E)
    }
    // Newton's method
    var x = M;
    for (var i=0; i < 2; i++) {
        x = x - f(x) / df(x);
    }
    return x;
}

// Calculate true anomaly from eccentricity (e) and eccentric anomaly (E)
// See: http://www.jgiesen.de/kepler/kepler.html
// See: https://en.wikipedia.org/wiki/True_anomaly
function phi(e, E) {
    return 2 * atan2(sqrt(1 + e) * sin(E / 2), sqrt(1 - e) * cos(E / 2));
}


// -------------------------------------------
// Compute Earth's rotation and Sun position
// -------------------------------------------

function rotateEarth(currentTime, latitude, longitude) {
    // Convert coordinates to radians
    latitude = latitude / 180 * pi;
    longitude = longitude / 180 * pi;

    // Rotate for latitude
    var rotation = rotateY(-latitude);
    // Rotate for longitude
    rotation = numeric.dot(rotateZ(longitude), rotation);
    // Correct for solstice longitude
    rotation = numeric.dot(rotateZ(-solstice_longitude / 180 * pi), rotation)
    // Spin the planet according to time since solstice
    rotation = numeric.dot(rotateZ(currentTime / sidereal_rotation * 2 * pi), rotation)
    // Tilt for earth's axis
    rotation = numeric.dot(rotateY(-axial_tilt / 180 * pi), rotation)

    return rotation;
}

function sunVector(currentTime, useEccentricity) {
    // -------------------------------------------------------------
    // What is the angle of the earth? (assuming circular orbit)
    var sun_angle = currentTime / 24.0 / 60 / 60 / sidereal_orbit * 2 * pi;

    // Get the correct sun_angle if we're using an elliptical orbit
    if (useEccentricity) {
        var mean_anomaly = (currentTime - solstice_to_perihelion) / 24.0 / 60 / 60 / sidereal_orbit * 2 * pi;
        var solstice_mean_anomaly = -solstice_to_perihelion / 24.0 / 60 / 60 / sidereal_orbit * 2 * pi;
        var solstice_angle = phi(orbit_eccentricity, E(orbit_eccentricity, solstice_mean_anomaly));
        var true_eccentricity = phi(orbit_eccentricity, E(orbit_eccentricity, mean_anomaly));
        sun_angle = true_eccentricity - solstice_angle;
    }

    // Get the position of the earth
    var earth = [[-1], [0], [0]];
    earth = numeric.dot(rotateZ(sun_angle), earth);

    // Sun vector is the inverse of the Earth vector
    var sun = numeric.neg(earth);
    return sun;
}


// -------------------------------------------
// Azimuth of the Sun, from Earth
// -------------------------------------------

function azimuth(currentTime, latitude, longitude, useEccentricity) {
    var sun = sunVector(currentTime, useEccentricity);
    var rot = rotateEarth(currentTime, latitude, longitude);
    var dotN = numeric.dot(numeric.transpose(sun), numeric.dot(rot, north));
    var dotE = numeric.dot(numeric.transpose(sun), numeric.dot(rot, east));
    var angle = atan2(dotE, dotN);
    return angle;

}


// -------------------------------------------
// Two-way interactive bindings to user interface
// -------------------------------------------

var Model = function (perspective, currentHour, latitude, longitude) {
    self = this;
    now = new Date();

    // Inputs: Date
    var currentYear = now.getFullYear();
    var currentMonth = now.getMonth() + 1;
    var currentDay = now.getDate();
    var currentTimeZone = 1;
    
    // Inputs: Position
    var useEccentricity = true;

    // Outputs: Time
    var currentUTC = function (){
        var utc = Date.UTC(currentYear, currentMonth - 1, currentDay, currentHour, 0);
        utc = utc - currentTimeZone * 1000 * 60 * 60;

        return utc;
    };

    // Outputs: Sun Location, converted to degrees
    var az = toDeg(azimuth((currentUTC() - summer_solstice) / 1000.0, latitude, longitude, useEccentricity));
    az = Math.round(az);
    
    switch (perspective) {
        case 'northeast':
            az += 45;
            break;
        case 'east':
            az += 90;
            break;
        case 'southeast':
            az += 135;
            break;
        case 'south':
            az += 180;
            break;
        case 'southwest':
            az += 225;
            break;
        case 'west':
            az += 270;
            break;
        case 'northwest':
            az += 315;
            break;
    }

    if (az >= 360)
        az -= 360;

    if (az <= 22.5 || az > 337.5)
        self.azimuth = 'North';
    else if (az <= 67.5)
        self.azimuth = 'NorthEast';
    else if (az <= 112.5)
        self.azimuth = 'East';
    else if (az <= 157.5)
        self.azimuth = 'SouthEast';
    else if (az <= 202.5)
        self.azimuth = 'South';
    else if (az <= 247.5)
        self.azimuth = 'SouthWest';
    else if (az <= 292.5)
        self.azimuth = 'West';
    else if (az <= 337.5)
        self.azimuth = 'NorthWest';
};