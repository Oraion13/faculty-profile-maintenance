/* ----------------------- Register ------------------------ */

const register = {
    "full_name": "full name",
    "username": 1234,
    "email": "example@email.com",
    "password": "password"
}

/* ------------------------ Login ----------------------- */

const login = {
    "username": 1234,
    "password": "password"
}

const login1 = {
    "username": "example@email.com",
    "password": "password"
}

/* -------------------------- Forget Password --------------------- */

const forget_password = {
    "email": "example@email.com"
}

/* -------------------------- Reset Password --------------------- */

const reset_password = {
    "password": "new password"
}

/* --------------------------- User Details -------------------- */

const User_info_api = {
    "phone": 1234567890,
    "address": "654, south street, Tirunelveli, tvl - 627 600",
    "position_id": 4,
    "department_id": 3,
    "position_present_where": "College of Engineering Guindy, Anna University, Chennai",
    "position_present_from": "2012-05-01"
}

/* --------------------------- Previous Positions -------------------*/
const Positions_prev_api = [
    {
        "position_prev_id": 1,
        "position_id": 4,
        "department_id": 2,
        "position_prev_where": "College of Engineering Guindy, Anna University, Chennai",
        "position_prev_from": "1993-05-02",
        "position_prev_to": "2000-07-07"
    },
    {
        "position_prev_id": 19,
        "position_id": 5,
        "department_id": 1,
        "position_prev_where": "MIT, Anna University, Chennai",
        "position_prev_from": "2001-05-02",
        "position_prev_to": "2003-07-07"
    }, {
        "position_prev_id": 18,
        "position_id": 4,
        "department_id": 2,
        "position_prev_where": "College of Engineering Trichy, Anna University, Trichy",
        "position_prev_from": "2004-05-02",
        "position_prev_to": "2008-08-01"
    },
    {
        "position_prev_id": 0, // For new data
        "position_id": 5,
        "department_id": 1,
        "position_prev_where": "Anna University Regional Campus Madurai, Madurai",
        "position_prev_from": "2010-11-12",
        "position_prev_to": "2015-08-08"
    }
]