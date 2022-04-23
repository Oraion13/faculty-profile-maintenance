/* ----------------------- Register ------------------------ */

const register = {
    "honorific": "Mr.",
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

const user_info = {
    "phone": 1234567890,
    "address": "654, south street, Tirunelveli, tvl - 627 600",
    "position_id": 4,
    "department_id": 3,
    "position_present_where": "College of Engineering Guindy, Anna University, Chennai",
    "position_present_from": "2012-05-01"
}

/* --------------------------- Previous Positions -------------------*/
const positions_prev = [
    {
        "position_prev_id": 0, // For new data
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
        "position_prev_id": 20,
        "position_id": 5,
        "department_id": 1,
        "position_prev_where": "Anna University Regional Campus Madurai, Madurai",
        "position_prev_from": "2010-11-12",
        "position_prev_to": "2015-08-08"
    }
]

/* --------------------------- Memberships -------------------*/

const memberships = [
    {
        "membership_id": 0, // For new data
        "membership": "IEEE: 9532143"
    },
    {
        "membership_id": 2,
        "membership": "CSI - Life Member : 54353454 "
    }
]

/* --------------------------- Area of specialization -------------------*/

const area_of_specialization = [
    {
        "specialization_id": 0, // For new data
        "specialization": "INFORMATION SECURITY"
    },
    {
        "specialization_id": 11,
        "specialization": "CLOUD COMPUTING"
    }
]

/* --------------------------- Present Additional Responsibilities -------------------*/

const additional_responsibilities_present = [
    {
        "additional_responsibility_present_id": 0, // For new data
        "additional_responsibility_present":
            "ATTESTING OFFICER, Office of the Controller of Examinations, Anna University, Tirunelveli",
        "additional_responsibility_present_from": "2015-05-05"
    },
    {
        "additional_responsibility_present_id": 6,
        "additional_responsibility_present":
            "Student Counsellor, Department of Mechanical Engineering, Anna University,Chennai",
        "additional_responsibility_present_from": "2020-05-05"
    }
]

/* --------------------------- Invited Lectures -------------------*/

const invited_lectures = [
    {
        "invited_lecture_id": 0, // For new data
        "invited_lecture": "Delivered a Lecture on Network Programming, Security and Managemen in Faculty Development Programme organized by RCC, Anna University, Chennai - 600 025",
        "invited_lecture_at": ""
    },
    {
        "invited_lecture_id": 3,
        "invited_lecture": "Delivered a Lecture on Unix and Network Programming in Faculty Development Programmeorganized by Department of Information Technology , MIT, Anna University, Chennai 600 044",
        "invited_lecture_at": "2015-09-08"
    }
]

/* --------------------------- Previous Additional Responsibilities -------------------*/

const additional_responsibilities_prev = [
    {
        "additional_responsibility_prev_id": 0, // For new data
        "additional_responsibility_prev":
            "Scrutiny Officer, MIT, Anna University, Chennai",
        "additional_responsibility_prev_from": "2005-09-08",
        "additional_responsibility_prev_to": ""
    },
    {
        "additional_responsibility_prev_id": 4,
        "additional_responsibility_prev":
            "Anti-ragging Committee - Coordinator, University College of Engineering, Arni, Anna University, Chennai",
        "additional_responsibility_prev_from": "2012-05-09",
        "additional_responsibility_prev_to": "2015-03-18"
    }
]

/* -------------------------------------- Degree ------------------------------------*/

const degree = [
    {
        "degree_id": 0, // for new data
        "degree":
            "M.E. in COMPUTER SCIENCE ENGINEERING , MADURAI KAMARAJAR UNIVERSITY",
        "degree_from": "2006-09-08",
        "degree_to": "2009-03-01"
    },
    {
        "degree_id": 5,
        "degree": "M.B.A. ANNAMALAI UNIVERSITY, ANNAMALAI UNIVERSITY",
        "degree_from": "2010-09-08",
        "degree_to": "2012-03-01"
    }
]

/* -------------------------------------- Other Employment ------------------------------------*/

const other_employment = [
    {
        "other_employment_id": 0, // For new data
        "other_employment": "Assistant Professor, Annamalai University",
        "other_employment_from": "2001-09-08",
        "other_employment_to": "2009-03-01"
    },
    {
        "other_employment_id": 3,
        "other_employment": "Professor, CEG",
        "other_employment_from": "2010-09-08",
        "other_employment_to": "2015-03-01"
    }
]

/* -------------------------------------- Programme Attended ------------------------------------*/

const programme_attended = [
    {
        "programme_attended_id": 0, // For new data
        "programme_attended": "Attended a National level Short Course on AICE-ISTE Winter School on Advanced Java Programming organized by PSG College of Technology",
        "programme_attended_from": "2002-09-08",
        "programme_attended_to": "2002-09-12"
    },
    {
        "programme_attended_id": 3,
        "programme_attended": "Participated in a National level workshop on Object Oriented Analysis and Design using UML and Fundamentals of Rational Rose organized by Rational Software Development Company, India",
        "programme_attended_from": "2005-09-08",
        "programme_attended_to": ""
    }
]

/* -------------------------------------- Programme Chaired ------------------------------------*/

const programme_chaired = [
    {
        "programme_chaired_id": 0, // For new data
        "programme_chaired": "Attended a National level Short Course on AICE-ISTE Winter School on Advanced Java Programming organized by PSG College of Technology",
        "programme_chaired_from": "2002-09-08",
        "programme_chaired_to": "2002-09-12"
    },
    {
        "programme_chaired_id": 3,
        "programme_chaired": "Participated in a National level workshop on Object Oriented Analysis and Design using UML and Fundamentals of Rational Rose organized by Rational Software Development Company, India",
        "programme_chaired_from": "2005-09-08",
        "programme_chaired_to": ""
    }
]

/* -------------------------------------- Programme Organized ------------------------------------*/

const programme_organized = [
    {
        "programme_organized_id": 0, // For new data
        "programme_organized": "Attended a National level Short Course on AICE-ISTE Winter School on Advanced Java Programming organized by PSG College of Technology",
        "programme_organized_from": "2002-09-08",
        "programme_organized_to": "2002-09-12"
    },
    {
        "programme_organized_id": 3,
        "programme_organized": "Participated in a National level workshop on Object Oriented Analysis and Design using UML and Fundamentals of Rational Rose organized by Rational Software Development Company, India",
        "programme_organized_from": "2005-09-08",
        "programme_organized_to": ""
    }
]

/* -------------------------------------- Special Reprasentations ------------------------------------*/

const special_reprasentations = [
    {
        "special_reprasentation_id": 0, // For new data
        "special_reprasentation": "Reviewer in The International Journal of Computers and Applications,ACTA",
        "special_reprasentation_from": "2002-09-08",
        "special_reprasentation_to": "2002-10-12"
    },
    {
        "special_reprasentation_id": 1,
        "special_reprasentation": "Advisory Board Member in International Congress on Pervasive Computing and Management",
        "special_reprasentation_from": "2005-09-08",
        "special_reprasentation_to": ""
    }
]

/* -------------------------------------- Books Published ------------------------------------*/

const books_published = [
    {
        "book_published_id": 0, // For new data
        "title": "Communications in Computer and Information Science",
        "description": "978th edition authored by Vijayakumar P, Vijayakumar P and Kannan A and published by CCIS-Springer, The Gandhigram Rural Institute-Deemed to be Univer.",
        "published_at": "2002-10-12"
    },
    {
        "book_published_id": 1,
        "title": "Information Security",
        "description": "authored by S.Bose and published by Centre for Distance Education Anna University Chennai, Tamil Nadu, India",
        "published_at": ""
    }
]

/* -------------------------------------- Papers Published ------------------------------------*/

const papers_published = [
    {
        "paper_published_id": 0, // For new data
        "paper_published": "The Icfai Journal of Systems Management, The Icfai Journal of Systems Management, published by The Icfai University Press. Vol. 4, Issue 4, pp. 14-24",
        "paper_published_at": "2006-09-08",
        "is_international": 1
    },
    {
        "paper_published_id": 1,
        "paper_published": "Adaptive multipath multimedia streaming architecture for mobile networks with proactive buffering using mobile proxies, Journal of computing and information technology, published by SRCE-SveuÄ•iliÅ¡ni raÄ•unski centar. Vol. 15, Issue 3, pp. 215-226",
        "paper_published_at": "2009-08-07",
        "is_international": 0
    }
]

/* -------------------------------------- Papers Presented ------------------------------------*/

const papers_presented = [
    {
        "paper_presented_id": 0, // For new data
        "paper_presented": "The Icfai Journal of Systems Management, The Icfai Journal of Systems Management, published by The Icfai University Press. Vol. 4, Issue 4, pp. 14-24",
        "paper_presented_at": "2006-09-08",
        "is_international": 1
    },
    {
        "paper_presented_id": 1,
        "paper_presented": "Adaptive multipath multimedia streaming architecture for mobile networks with proactive buffering using mobile proxies, Journal of computing and information technology, published by SRCE-SveuÄ•iliÅ¡ni raÄ•unski centar. Vol. 15, Issue 3, pp. 215-226",
        "paper_presented_at": "2009-08-07",
        "is_international": 0
    }
]

/* -------------------------------------- Patents ------------------------------------*/

const patents = [
    {
        "patent_id": 0, // For new data
        "patent": "Filed patent rights for Mini PC Device for Computer Tablein India.",
        "file_number": 656561,
        "patent_at": "2007-01-05"
    },
    {
        "patent_id": 1,
        "patent": "Sample 2",
        "file_number": 532554,
        "patent_at": ""
    }
]

/* -------------------------------------- Experience Abroad ------------------------------------*/

const exp_abroad = [
    {
        "exp_abroad_id": 0, // For new data
        "exp_abroad": "Visited Thiyagarajar College of Engineering Alumni Association, Dubai",
        "exp_abroad_from": "2012-08-09",
        "exp_abroad_to": "2012-10-11",
        "purpose_of_visit": ""
    },
    {
        "exp_abroad_id": 1,
        "exp_abroad": "Visited WORLDCOMP-16 , USA",
        "exp_abroad_from": "2016-06-08",
        "exp_abroad_to": "",
        "purpose_of_visit": "To attend and present a paper in the International Conference."
    }
]

/* -------------------------------------- Research Degree ------------------------------------*/

const research_degree = [
    {
        "research_degree_id": 0,
        "research_degree": "Ph.D. in INFORMATION SECURITY from Faculty of INFORMATION AND COMMUNICATION ENGINEERING, COLLEGE OF ENGINEERING GUINDY, ANNA UNIVERSITY",
        "research_degree_from": "2001-08-09",
        "research_degree_to": "2007-10-11",
        "title": "AN INTELLIGENT MULTI-LAYER ANOMALY INTRUSION DETECTION AND PREVENTION MODEL FOR AD-HOC NETWORKS ."
    },
    {
        "research_degree_id": 1,
        "research_degree": "Ph.D. in Multimedia Networks and systems from Faculty of Information and Communication, CEG, Anna University",
        "research_degree_from": "2007-08-09",
        "research_degree_to": "2014-10-11",
        "title": "VIPV: AN EFFECTIVE STREAMING SYSTEM FOR VCR INTERACTIVITY IN PEER-TO-PEER VIDEO-ON-DEMAND SYSTEMS."
    }
]

/* -------------------------------------- Extension and Outreach Programme ------------------------------------*/

const extension_outreach = [
    {
        "extension_outreach_id": 0, // For new data
        "extension_outreach": "Special Observer, H.Sc Tamil Nadu Government Examination , participated by 5000 and funded by Tamil Nadu Government at Madurai District during and",
        "extension_outreach_from": "2001-08-09",
        "extension_outreach_to": "2003-10-11",
        "number_of_participants": "" // can be 0
    },
    {
        "extension_outreach_id": 1,
        "extension_outreach": "Program Officier, Special Annual Camp by NSS, participated by 100 and funded by Anna University at Shomangalam Village in Kanchipuram",
        "extension_outreach_from": "2001-08-09",
        "extension_outreach_to": "2002-10-11",
        "number_of_participants": 95
    }
]

/* -------------------------------------- Sponsored Projects Completed ------------------------------------*/

const sponsored_projects_completed = [
    {
        "project_id": 0, // For new data
        "project": "A Virtual Walk Through Of A Home, funded by Solutionstar Services India Private Limited",
        "project_from": "2001-08-09",
        "project_to": "2002-10-11",
        "project_cost": 31.43
    },
    {
        "project_id": 4,
        "project": "Cross Lingual Information Access System",
        "project_from": "2004-08-09",
        "project_to": "2005-10-11",
        "project_cost": "" // can be 0
    }
]

/* -------------------------------------- Research Guidance ------------------------------------*/

const research_guidance = [
    {
        "research_guidance_id": 0, // For new data
        "phd_guided": 4,
        "phd_guiding": 5,
        "me_guided": 7,
        "me_guiding": 6,
        "ms_guided": 0,
        "ms_guiding": 0
    },
    {
        "research_guidance_id": 1,
        "phd_guided": 1,
        "phd_guiding": 2,
        "me_guided": 3,
        "me_guiding": 4,
        "ms_guided": 5,
        "ms_guiding": "" // can be 0
    }
]

/* -------------------------------------- Research Guidance ------------------------------------*/

const photo = {
    "photo_name": "name",
    "photo_type": "image/png",
    "photo": "tmp_name..."
}