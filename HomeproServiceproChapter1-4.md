### **HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and Maintenance Services** 

**________________** 

**A Capstone Project Presented to The Faculty of the College of Computer Studies Tarlac State University Tarlac City** 

**________________** 

**In Partial Fulfillment of the Requirements for the Degree In Technical Service Management** 

**________________ by: Muring, Sheena Aira L. Pablo, Maria Althea B. Quial, Yuan Emmanuel L. Urquico, Kevin Brian** 

**March 2026** 

ii 

## **TABLE OF CONTENTS** 

|**TABLE OF CONTENTS ................................................................................................. ii**|
|---|
|**LIST OF TABLES ........................................................................................................... iii**|
|**LIST OF FIGURES ......................................................................................................... iv**|
|**1 INTRODUCTION...........................................................................................................1**|
|1.1 Project Context.........................................................................................................1|
|1.2 Purpose and Description ..........................................................................................3|
|1.3 Objectives ................................................................................................................5|
|1.4 Scope and Limitations..............................................................................................6|
|**2 REVIEW OF RELATED LITERATURE ...................................................................8**|
|2.1 Related Literature.....................................................................................................8|
|2.2 Related Studies.......................................................................................................18|
|2.3 Conceptual Framework ..........................................................................................20|
|2.4 Definition of Terms................................................................................................22|
|**ISO/IEC 25010 .................................................................................................................22**|
|**3 TECHNICAL BACKGROUND ..................................................................................24**|
|3.1 System Architecture ...............................................................................................24|
|3.2 Peopleware .............................................................................................................31|



|3.3 Sources of Data ......................................................................................................32|
|---|
|**4 METHODOLOGY .......................................................................................................33**|
|4.1 Methods in Data Gathering ....................................................................................33|
|4.1.1 Interview Method ..........................................................................................34|
|4.1.2 Internet Method .............................................................................................35|
|4.1.3 Observation ...................................................................................................35|
|4.1.4 Respondents of the Study..............................................................................36|
|4.1.5 Research Instruments ....................................................................................37|
|4.1.6 Statistical Treatment .....................................................................................39|
|4.2 Story Board ............................................................................................................42|
|4.3 Design and Development of HomeService Pro: A Scheduling and Management|
|Platform for Home Appliance Repairs and Maintenance Services .............................48|
|4.3.1 Requirement Analysis and Documentation ..................................................48|
|4.3.1.1 User Requirements ...............................................................................48|
|4.3.1.2 User Characteristics .............................................................................49|
|4.3.1.3 Functional Requirements .....................................................................50|
|4.3.1.4 Non-Functional Requirements .............................................................51|
|4.3.2 Design of Software and/or System and/or Product and/or Processes ...........51|
|4.3.2.1 ER Diagram .........................................................................................52|
|4.3.2.2 Functional Decomposition Diagram ....................................................53|
|4.3.2.3 Operating Environment ........................................................................54|



|4.3.2.4 Design and Implementation Constraints ..............................................54|
|---|
|4.3.3 System Development Methodology ..............................................................55|
|4.3.3.1 Planning ...............................................................................................55|
|4.3.3.2 Scrum ...................................................................................................55|
|4.3.3.3 Project Schedule...................................................................................57|
|4.4 To evaluate the performance of HomeService Pro: A Scheduling and|
|Management Platform for Home Appliance Repairs and Maintenance Services ........58|
|4.4.1 ISO/IEC 25010 Software Quality Evaluation ...............................................58|
|**APPENDICES ..................................................................................................................64**|
|Appendix A: First Technical Adviser Capstone Consultation .....................................64|
|**REFERENCES .................................................................................................................67**|



## **LIST OF TABLES** 

|**Table 1. Literature Matrix……………………………………………………………..13**|
|---|
|**Table 2. Functional and Feature Matrix……………………………………………....18**|
|**Table 3. Software Development Requirements……………………………………….24**|
|**Table 4. Hardware Development Requirements……………………………………...29**|
|**Table 5. Functional Requirement for the user………………………………………..50**|
|**Table 6. Non-Functional Requirement for Safety and Security of HomeService Pro:**|
|**A Scheduling and Management Platform for Home Appliance Repairs and**|
|**Maintenance Services……………………………………………………………..51**|
|**Table 7. Operating Environment of  HomeService Pro: A Scheduling and**|
|**Management Platform for Home Appliance Repairs and Maintenance Services**<br>**………………………………………………………………………………………54**|
|**Table 8. Design and Implementation Constraints of HomeService Pro: A Scheduling**|
|**and Management Platform for Home Appliance Repairs and Maintenance**|
|**Services……………………………………………………………….……………54**|
|**Table 9. IT Expert Evaluation for System Functionality…………………………….58**|
|**Table 10. IT Expert Evaluation Criteria for User Interface…………………………59**|



iv 

**Table 11.T Expert Evaluation Criteria for Reliability………………………………59 Table 12. End User Evaluation Criteria for Performance Efficiency………………60 Table 13.  End User Evaluation Criteria for Interaction Capability………...……...61 Table 14. IT Expert Evaluation Criteria for Security………………………………..62** 

**Table 15. IT Expert Evaluation Testing for Functionality………………………..….63** 

iv 

### **LIST OF FIGURES** 

|**Figure 1. Conceptual Framework……………………………………………………..20**|
|---|
|**Figure 2. Likert Scale…………………………………………………………………..38**|
|**Figure 3. Design Mockup Log-in System……………………………………………...44**|
|**Figure 4. Design Mockup for Service Location……………………………………….44**|
|**Figure 5. Appliance Repair Selection………………………………………………….45**|
|**Figure 6. Design Mockup for Technician Selection…………………………………...46**|
|**Figure 7. Design Mockup for Chat and Locator……………………………………...47**|
|**Figure 8.** **Use Case Diagram of HomeService Pro: A Scheduling and Management**|
|**Platform for Home Appliance Repairs and Maintenance Services……………48**|
|**Figure 9. Entity Relationship Diagram………………………………………………..52**|
|**Figure 10. Functional Decomposition Diagram………………………………………53**|
|**Figure 11. Burndown Chart…………………………………………………………...57**|



1 

## **1 INTRODUCTION** 

## **1.1 Project Context** 

<mark>In this modern era, the rapid advancement of technology has transformed electronic appliances from luxuries into everyday necessities. Households and businesses increasingly depend on these appliances to enhance productivity and efficiency. However, as reliance on technology grows, so does the need for regular maintenance and repair services.</mark> 

<mark>Deshmukh et al. (n.d.) noted that managing service requests for electronic gadgets and appliances has become increasingly complex due to the rising number of users and service demands, often resulting in inefficiencies within traditional systems. Moreover, the effectiveness of service delivery depends not only on technicians’ technical skills but also on a robust management system and strong customer engagement. May and Aman (2021) observed that many appliance repair businesses still rely on manual record-keeping methods, such as logbooks and paper-based documentation, which are prone to data loss, security issues, and inaccuracies. Muhamad et al. (2024) further emphasized that modernizing appliance service management through digital systems can significantly improve operational efficiency and service quality.</mark> 

2 

<mark>Customer satisfaction and business competitiveness are strongly influenced by the quality of service delivery. Nasir et al. (2024) highlighted that after-sales service plays a crucial role in building brand reputation and fostering long-term customer relationships. Suh (2025) added that understanding customer segments through interaction behaviors enables businesses to offer more personalized services. This is supported by Tomić et al. (2025), who found that well-designed websites have a significant positive impact on customer satisfaction and purchase intention.</mark> 

<mark>Despite technological advancements, accessing reliable repair services remains a major challenge for many users. Ruaya (2023) observed that traditional methods—such as relying on personal recommendations or classified listings—are time-consuming and offer limited options. Similarly, Benhadi and Ghouali (2023) pointed out that connecting users with qualified technicians is often inefficient without a centralized digital platform. Alvarado Baudat et al. (2025) noted that the lack of accessible technological solutions also limits job opportunities for independent technicians, who heavily depend on referrals and slow communication channels.</mark> 

<mark>Efficient technician routing and scheduling are critical to service operations. Nunes and Lopes (2022) demonstrated that poor scheduling systems can lead to delays, reduced productivity, and lower customer satisfaction. In today’s competitive market, companies must respond quickly and coordinate services effectively. Chowdhury et al. (2025) showed that integrating features such as technician hiring, repair booking, and real-time scheduling into a single platform can greatly enhance the overall user experience.</mark> 

3 

<mark>Service centers continue to face persistent challenges, including poor record management, inefficient handling of service requests, difficulty in finding reliable technicians, limited customer accessibility, and ineffective scheduling systems. These issues contribute to operational inefficiencies, reduced job opportunities for new technicians, and lower customer satisfaction.</mark> 

<mark>In conclusion, there is a clear need for an integrated technological solution to address these problems. The proponents propose the development of HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and Maintenance Services. This web-based platform aims to streamline service requests, improve clienttechnician matching, enhance scheduling efficiency, and provide a centralized system for managing appliance repair services.</mark> 

## **1.2 Purpose and Description** 

The objective of  this project is to develop an interactive web-based platform that manages appliance repair services efficiently. It replaces manual logbooks and paper-based records with a secure digital record management system, while also providing online scheduling features for efficient service requests. The system enables users to find and select verified technicians in their will, it supports optimized routing and scheduling, and includes a real-time communication and service tracking to ensure smooth coordination between customers and technicians. 

4 

This project supports the current level of efficiency, accuracy, and convenience in managing repair services. It helps keep the processing of requests timely, reduces errors from manual recording, and allows clear communication between users and technicians. In addition, it organizes service data in a more structured way, lets users track the progress of their requests, and gives technicians more job opportunities while helping them manage their workload effectively 

The integration of the multiple integrated features into one platform is innovative and relevant in today’s standard. It also combines technician matching,  real-time communication, service tracking, and intelligent scheduling to address issues to traditional repair services. The study transforms a traditional repair into a digitalized system, and it remains highly relevant to growing dependence on  appliances and due to increase of demand for fast,reliable, accessible repair services in the modern communities. 

5 

## **1.3 Objectives** 

Our general objective for this study is to design, implement and develop “HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and Maintenance Service” that can let our customer be able to look for a verified technician and improve communication between Customer and Technicians. This study also enhances technician accessibility and increases customer satisfaction through a digital platform. 

- a. To design and develop a web-based scheduling and management system for home appliance repair and maintenance services that includes major functions such as user registration and login, technician profile management, technician matching, online booking/scheduling, service tracking, customer feedback, and administrative monitoring. 

- b. To integrate network and web-based technologies that support the system’s core functions, including map-based technician and client location, real-time tracking of service requests, database connectivity, SMS and in-website notifications, and communication between customers and technicians. 

- c. To evaluate the developed system based on the ISO/IEC 25010 Software Quality Model, particularly in terms of functional suitability, usability, reliability, security, and maintainability, in order to determine its effectiveness and acceptability. 

6 

## **1.4 Scope and Limitations** 

This study focuses on the design, development, and implementation of “HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and Maintenance Services”. It is a web-based system designed that has three versions for server, user, and technicians to enhance efficiency, accessibility, and service quality within the community by offering a comprehensive solution for handling service requests, appointment scheduling, communication between customers and technicians, service progress tracking, and customer feedback collection through ratings and evaluations. Additionally, it allows technicians to manage their profiles, increase client visibility, and organize daily schedules effectively. For customers, it allows them to receive a SMS and e-mail notification for updates. 

The system will include major functions such as user registration and login, technician profile, technician matching, online booking/scheduling, service requests, mapbased technician/client location, service tracking and monitoring, SMS and e-mail notifications, customer feedback, and administrative monitoring. These features are intended to help customers to locate verified technicians more easily, monitor the status of the repair requests. 

7 

The system will be built on a three-tier architecture consisting of the presentation, application, and data layers, integrating various technologies such as frontend frameworks (HTML, CSS, JavaScript, React.js, Vue.js), backend frameworks (Node.js, Express, Django, Laravel), databases (MySQL, MongoDB), API (Auth0, Firebase, Google Calendar API, IP Geolocation API OpenStreetMap, OneSIgnal), Analytics (Python Scripts, chart.js), Search/Knowledge Base (MySQL), Security (Role Base Access Control, password hashing, input validation), Real-time updates (AJAX, Flask-SocketIO). 

This study is limited to home appliance repair and maintenance services only, excluding other services like plumbing, electrical, or automotive repairs. The platform will be developed as a web-based system. Advanced features like a standalone mobile app, cashless payment transaction, and AI-based predictive maintenance are not included in the study’s scope. The system depends on stable internet connectivity and will not support offline functionality. Despite these limitations, the goal is to develop a secured, structured, and accessible digital platform that addresses current accessibility issues, communication challenges commonly experienced in traditional home appliance repair service providers and by the customers reachability. 

8 

## **2 REVIEW OF RELATED LITERATURE** 

## **2.1 Related Literature** 

This study focused on the development and assessment of an online local home services application system for maintenance and repairs. The system is designed to address the growing need for a convenient and efficient way to access home maintenance and repair services. The study involved the development of a digital platform that allows homeowners to connect with local service providers who can help them with various household tasks.[12] 

The creation of a web application that links customers and service providers for convenient on-demand services delivered right to their door is the goal of this project. Through the use of the internet, customers can make appointments, peruse a range of service categories, and obtain assistance. The user-friendly interface and efficient booking process will make it easy to find and schedule service specialists, which will improve user experience and foster a vibrant on-demand service ecosystem. [13] 

This system helps the service centre to automate its business activities effectively and manage data securely. This system also presents user-friendly functionality and provides convenience to the target users.[14] 

Ruaya, P. (2023) in their study “Development and Assessment of Online Local Home Services Application System for Maintenance and Repairs” the development and evaluation of an 

9 

internet-based application system for home maintenance services and repairs is a big step forward for home services. The system helps homeowners find services easily and service providers can reach customers. The system is very easy to use, has features, and works well on different devices. It meets the needs of both homeowners and service providers. The system's security keeps user information and its easy to manage and expand. The evaluation score for the system was really good with a score of 4.35 out of 5.  The system's usability scored 4.5 out of 5 which means it's simple and straightforward for everyone. The system's functionality scored 4.3 out of 5 which shows it has all the features for homeowners and service providers to communicate. The system's portability scored 4.2 out of 5 meaning it works on devices and platforms. The system's ease of maintenance scored 4.4 out of 5 showing it's easy to maintain and upgrade. The results show the system is successful and offers a platform for homeowners and service providers to connect and get services done. The system really works for home maintenance and repairs. It helps homeowners and service providers link up. [15] 

In the study titled “ Streamlined On-Demand Home Services: Connecting Customers with Expert Providers Through a Web Application” by Katukam, S. et al., (2024, December),  Specialists contend that we are currently experiencing an information era, This age is bringing changes to our economy, society and culture. These changes are similar to those that happened during the revolution. A major change is happening in how we do business. We used to do things on paper. Now we do them electronically. This article looks at the benefits of e-commerce. How it's changing the market. It focuses on how digital transactions are changing how people, businesses and governments buy and sell things. China's economy is growing fast. The service industry has become very important to its market. Businesses are now competing on how good their service's. Customer satisfaction and loyalty are key to success. This study looks at a household appliance sales and service center. It uses surveys 

10 

and analysis to find out what makes customers happy. The results will help create plans to improve service quality. This will make the business more competitive, in the market.[16] 

A research conducted by Saundariya, K. et al., (2021, May), discussed the Webapp Service for Booking Handyman Using Mongodb, Express JS, React JS, Node JS that there has been a big increase in the need for handyman services everywhere.When people have problems at home some issues can be really stressful. They can't fix them on their own.People are busy with their schedules so they need workers to help maintain and repair their homes.It can be hard to find workers in person at the time and cost.That's why this website makes it easy to book your workers at the right time and cost. With one click workers can be at your doorstep. Handyman workers have their login to show who they are by adding the work and skills they have. This helps professionals get opportunities and earn money based on their work. The website has categories and services. When users log in to find a service, workers are listed based on their location, cost, name and contact information. This website was built using React JS, which makes it fast boosts productivity and is easy to find on search engines. It uses MongoDB, a database that's easy to manage and grow. With this website users can avoid delays and difficulties. The website helps people find handyman services they need. Workers get to showcase their skills and earn money. It makes it easy for people to get help at home. Workers get more opportunities.[17] 

Aji, A. et al.,(2022) in their study “Quick Reacher: An Application for Home Services”, the researcher presents Quick Reacher, a web-based tool aimed at reducing the pressures of contemporary work environments by offering a one-click remedy for home maintenance problems. Leveraging GPS technology, the system retrieves a user's location to find and assign the closest available service provider, while enabling users to sort professionals according to reviews and ratings. The application 

11 

features a flexible structure made up of four main components—Admin, Shop, Service Provider, and User—encompassing three essential areas: Home Maintenance, Cleaning Services, and Appliance Repairs. A notable aspect of this platform is the built-in shopping feature, allowing customers to promptly acquire essential repair supplies from suggested local stores, bypassing long lines and conserving time. Created with Python (Flask) and Android for the front end along with a MySQL backend, the system seeks to deliver a dependable, transparent, and professional service atmosphere. The research emphasizes that by providing adaptable fees and round-the-clock access to a pool of skilled laborers, the app effectively connects busy homeowners with proficient technicians. In the end, the researchers determine that Quick Reacher successfully simplifies household tasks with an effective, online interface that is accessible "from everywhere to anywhere."[18] 

Donaldo, E. et al.,(2025) also conducted research about digital transformation strategy for JQ Aircon and Refrigeration Services, a family-run enterprise in Panabo City that presently depends on manual methods for its everyday operations. Through the identification of key challenges like scheduling setbacks, human mistakes in record maintenance, and poor inventory monitoring, the researchers created two main digital solutions: the Service Scheduling and Inventory Management System (SSIMS) and the Customer Appliance Report and Evaluation Form (CARE). SSIMS automates appointment scheduling and enables real-time tracking of spare parts, whereas CARE emphasizes digitalizing post-service records and collecting customer input to improve service quality. The research finds that adopting this cohesive IT framework—backed by particular hardware, software, and a committed IT expert—will greatly enhance the firm's operational efficiency, transparency, and overall customer satisfaction. [19] 

12 

With all these advancements there are still some limitations in the current systems. Most platforms only do things like booking and communication and do not use intelligent scheduling and optimization techniques. Also while some applications have time and location-based features there is still not enough integration between advanced algorithms and service management systems. This means that current solutions only address some parts of the service delivery and do not provide an integrated and automated system. 

Overall the studies show that while current home service platforms have improved accessibility and convenience there is still a need for advanced and intelligent systems. Home service platforms need to focus on integrating optimization models, real-time data processing and automated scheduling mechanisms into user- web platforms. This would make the systems more efficient, improve resource allocation and provide a solution for home maintenance and repair services. 

A strong three-tier design, which efficiently divides the platform into a presentation layer for the user interface, an application layer for core logic and APIs, and a data layer for safe information storage, is the foundation of HomeService Pro's development. The researchers use a varied frontend stack, including HTML, Tailwind CSS, JavaScript, Bootstrap, React.js, and Vue.js, to create a seamless and dynamic experience. Frameworks like Node.js, Express, Python Django, and Laravel power the backend, whereas MongoDB for flexible storage and MySQL for structured queries manage data. <mark>The functionality of the system is further improved by incorporating specialized APIs, including Auth0 and Firebase for secure user authentication, the Google Calendar API for organizing schedules, and OpenStreetMap together with IP Geolocation for tracking technicians and clients via maps. The platform utilizes AJAX and Flask-SocketIO for real-time updates and communication, while data analytics are delivered through Python scripts and Chart.js. Security is a key priority, handled via</mark> 

13 

<mark>Role-Based Access Control (RBAC), password encryption, and rigorous input validation to guarantee user data remains protected.</mark> 

_Table 1 Literature Matrix_ 

|Reference|Description|Strength|Weakness|
|---|---|---|---|
|Ruaya, P.<br>(2023)|Development<br>and assessment<br>of an online<br>home services<br>application for<br>maintenance|The study’s primary strength lies<br>in its high quantitative validation,<br>evidenced by a robust 4.35 out of<br>5 average rating across essential<br>software quality metrics. By<br>evaluating the system through the<br>lenses of usability, functionality,<br>portability, and maintainability,<br>the research ensures a well-<br>rounded technical foundation.<br>Furthermore, the project<br>addresses a significant, real-world<br>market gap by providing a<br>streamlined, digital bridge<br>between homeowners and local<br>service providers, making it both<br>socially relevant and practically<br>applicable.|<br> <br>Focused primarily on basic<br>connectivity between<br>homeowners and providers<br>rather than complex<br>scheduling.|



14 

|Katukam,<br>S. et al.<br>(2024)|Research on<br>streamlined on-<br>demand services<br>and the shift from<br>paper-based to<br>digital transactions<br>.|A standout feature of this study<br>is its economic empowerment<br>for both sides of the<br>transaction. For the customer, it<br>transforms a stressful, manual<br>search into a streamlined digital<br>experience, providing<br>immediate relief during the<br>chaotic moving process. For the<br>service provider, the platform<br>acts as a powerful lead-<br>generation tool, offering a<br>professional digital presence<br>without the need for an<br>independent marketing budget.<br>By formalizing the "gig<br>economy" for home repairs, the<br>project creates a structured<br>environment where quality<br>work is rewarded with higher<br>visibility, effectively raising the<br>standard for local services.|The study currently lacks<br>a comprehensive security<br>and verification protocol,<br>which is essential for<br>building trust in "at-<br>home" service models.<br>While the technical<br>architecture is sound, the<br>proposal does not fully<br>address the complexities<br>of service accountability,<br>such as background<br>checks for workers or a<br>formal system for<br>resolving disputes.<br>Furthermore, the success<br>of the platform relies<br>heavily on the digital<br>participation of workers,<br>which may be hindered<br>by varying levels of tech-<br>savviness among<br>traditional handyman<br>fil|
|---|---|---|---|
||||proessonas.|
|Saundariy<br>a, K. et al.<br>(2021)|A web<br>application for<br>booking<br>handymen using<br>the MERN stack<br>(MongoDB,<br>Express, React,<br>Node).|This research addresses a critical<br>market gap by proposing a<br>digital solution to the logistical<br>challenges of home<br>maintenance. The integration of<br>**React JS**ensures a high-<br>performance, responsive<br>interface that mirrors the speed<br>of modern mobile apps, while<br>**MongoDB**offers the structural<br>flexibility needed to manage<br>varied service data efficiently.<br>By creating a centralized<br>marketplace, the study provides<br>a robust framework that|The study currently lacks<br>a comprehensive**security**<br>**and verification protocol**,<br>which is essential for<br>building trust in "at-<br>home" service models.<br>While the technical<br>architecture is sound, the<br>proposal does not fully<br>address the complexities<br>of**service accountability**,<br>such as background<br>checks for workers or a<br>formal system for<br>resolving disputes.|



15 

|||balances consumer demand for<br>transparency with the economic<br>empowerment of skilled<br>laborers.|Furthermore, the success<br>of the platform relies<br>heavily on the digital<br>participation of workers,<br>which may be hindered<br>by varying levels of tech-<br>savviness among<br>traditional handyman<br>professionals.|
|---|---|---|---|
|Aji, A. et<br>al. (2022)|"Quick Reacher,"<br>a web tool for<br>home<br>maintenance<br>using GPS and<br>Python (Flask).|A significant strength of this<br>study is its comprehensive<br>approach to solving common<br>household issues by integrating<br>multiple services—such as home<br>maintenance, cleaning, and<br>appliance repairs—into a single,<br>user-friendly platform. The<br>system utilizes GPS technology<br>to fetch a user's real-time<br>location and provides a list of<br>active, nearby service providers,<br>which enhances convenience<br>and efficiency. Additionally, the<br>inclusion of a feedback and<br>rating system for both service<br>providers and shops ensures a<br>level of reliability and quality<br>control, allowing users to make<br>informed decisions based on the<br>experiences of previous<br>consumers. The application also<br>features a unique integrated<br>marketplace that allows users to<br>immediately purchase necessary<br>materials for their services,<br>further streamlining the home<br>maintenance process|While the study presents<br>a robust functional<br>framework, a primary<br>weakness is the lack of<br>detailed empirical data or<br>user testing results to<br>validate the system's<br>actual performance and<br>user satisfaction in a real-<br>world setting. The paper<br>focuses heavily on<br>technical specifications<br>and module descriptions<br>but does not provide<br>quantitative analysis<br>regarding system latency,<br>the accuracy of its<br>location-based<br>"prediction" algorithm, or<br>its scalability during high<br>demand. Furthermore,<br>the study relies on a<br>relatively small number of<br>cited references (three),<br>which may limit its<br>grounding in current<br>broader research<br>regarding on-demand<br>service platforms or|



16 

||||diverse architectural<br>methodologies. Lastly,<br>the reliance on a<br>warning-based removal<br>system for providers after<br>only two complaints<br>might be overly simplistic<br>and could potentially lead<br>to the unfair removal of<br>legitimate professionals<br>without a more nuanced<br>dispute resolution<br>process.|
|---|---|---|---|
|Donaldo,<br>E. et al.<br>(2025)|Digital<br>transformation<br>strategy for a<br>local aircon<br>service using<br>SSIMS and CARE<br>systems .|The study provides a<br>comprehensive analysis of the<br>existing manual operational<br>challenges, such as scheduling<br>delays and human errors, which<br>establishes a strong justification<br>for the proposed digital<br>transformation. By designing<br>two specialized systems, the<br>Service Scheduling and<br>Inventory Management System<br>(SSIMS) and the Customer<br>Appliance Report and Evaluation<br>(CARE) Form,the researchers<br>offer a targeted solution that<br>addresses specific business<br>needs like automated booking<br>and real-time inventory<br>tracking. Furthermore, the study<br>is well-grounded in a local<br>context, aligning its objectives<br>with Philippine government<br>initiatives for MSME<br>digitalization and providing a<br>detailed financial breakdown for<br>hardware and software<br>implementation.|While the study excels in<br>conceptual design and<br>infrastructure planning, it<br>lacks a detailed<br>implementation timeline<br>or a pilot testing phase to<br>validate how the systems<br>will perform in a real-<br>world environment. The<br>plan primarily focuses on<br>the technical and<br>financial aspects of the<br>deployment but provides<br>limited information on<br>the long-term<br>maintenance of the<br>system or a<br>comprehensive risk<br>management strategy for<br>potential digital security<br>threats. Additionally, the<br>reliance on a single IT<br>Support Specialist for all<br>system maintenance and<br>troubleshooting might<br>create a bottleneck if the<br>business operations scale|



17 

|May, T.<br>P., &<br>Aman, H.<br>(2021)|A Development<br>of a<br>Management<br>Information<br>System for<br>Home Appliance<br>Repairing|The primary strength of this<br>study lies in its successful<br>development of a web-based<br>management system that<br>directly addresses the<br>inefficiencies of manual record-<br>keeping, such as data<br>redundancy and the risk of<br>physical data loss. The system<br>offers several advanced<br>functional features that were<br>found to be missing in existing<br>commercial solutions like Odoo<br>and Repairer, specifically the<br>inclusion of appointment<br>management, quotation<br>creation, and real-time<br>WhatsApp notifications for<br>technicians and customers.<br>Furthermore, the system<br>incorporates a responsive web<br>design and a comprehensive<br>reporting module that allows for<br>data-driven decision-making<br>through automatically<br>generated sales and frequency<br>charts.|quickly.<br>A notable weakness of<br>the study is the limitation<br>in its current data entry<br>efficiency, as the system<br>does not yet allow users<br>to insert multiple repair<br>job records<br>simultaneously. There is<br>also a lack of direct<br>interactivity for the<br>customer base beyond<br>basic status checking, as<br>the current version does<br>not include a dedicated<br>customer panel for online<br>repair requests.<br>Additionally, while the<br>system effectively tracks<br>progress, it lacks<br>proactive time-<br>management tools, such<br>as a countdown timer or<br>automated reminders to<br>alert technicians of<br>impending repair job<br>deadlines.|
|---|---|---|---|





<!-- Start of picture text -->
sormware | 1 | 2 | 3 | 4 | 5s | 6 | 7 | 8 | 9<br>Sears Home ff wf wf<br>Services<br>Local vf af af<br>Application<br>Quotes<br>FindGtocst | ¥ | |v | | | | | | |<br>HomeService v wf Vv vf vf af Vf Jf Jf<br>Pro<br><!-- End of picture text -->

19 

System, Chat Functionality, Location Selection, and Scheduling are commonly implemented, indicating that these are essential components for any home service platform. These functions support user accessibility, communication, and efficient booking processes, making them fundamental to system usability. 

On the other hand, features like Work Time Frame, Live Location, Technician Locator, Technician Selection, and Technician Rating are only partially implemented across the reviewed platforms. This suggests that while some systems provide advanced capabilities for tracking and selecting technicians, these features are not yet fully standardized. Their limited presence highlights an opportunity to enhance user experience by improving transparency, real-time monitoring, and service quality evaluation. 

Based on this analysis, the proposed system, HomeService Pro, will adopt both the common and advanced functionalities to create a more comprehensive and competitive platform. By integrating widely used features alongside enhanced capabilities such as live tracking and technician rating, the system aims to address existing gaps observed in current solutions. This approach is grounded in the review of existing platforms, ensuring that the development is aligned with proven practices while also introducing improvements that respond to user needs. 

## **2.3 Conceptual Framework** 



<!-- Start of picture text -->
e Requirements gathering and initial plan and scheduling between users<br>Knowledge Requirements + D DataParacelAnalysis  of the system5 + repairmenFunctional featuresand clients for both<br>« User Information ° Review and determine gathered results from + Enhances availability of local and<br>primary sources independent repairmen<br>© Client Information Identifying features needed for the system « Improved client's experience and<br>o Repairman Information based on sources satisfaction through seamless<br>; « System Design transactions<br>+ User Requirements m “ © Creating and designing a UI and layout for the<br>. Pill Sources/Information (Client and system, it includes:<br>epairmen)©o InterviewsSurveys — == Client/RepairmenDetails.  and status ofInformation repair.<br>© Questionnaires = User Dashboard<br>+ Relevant literatures and researches = Repairman availability<br>e Process Modeling<br>Process and System Requirements © Use Case Diagram [output<br>e Business Process Diagram<br>e Repair and Booking Information oe Database Schema HomeService Pro: A<br>*« SchedulingService Requirements Information « Systeme Development and Development creation of modules -——> SchedulingPlatform for and Home Management Appliance<br>* Schedule Availability = User (Repairmen, Client) Dashboard _ Repairs and<br>= User Authentication Maintenance Services<br>= Booking, scheduling and tracking<br>features<br><!-- End of picture text -->

21 

plan’s specifications and definition, and these are built in the system’s frontend, followed by the process modeling, which will be implemented to define and construct the scope and usage of the system within specific area and use cases, these include diagrams and database schema, and lastly, the system development, which involves the creation of system modules that includes overall functions and features of the system such as user dashboards and authentication and these are built behind the system’s backend. 

The direct outcome of this process is the output, the fully developed and functional HomeService Pro: Scheduling and Management Platform for Appliance Repairs and Management Services, and the successful implementation of this output leads to the desired Outcome; Facilitating faster booking and scheduling of repairs, functional features for clients and repairmen, enhances availability of local and independent repairmen for localized clients, and improving client and user satisfaction. 

## **2.4 Definition of Terms** 

22 

**API** <mark>APIs, or Application Programming Interfaces, are the invisible backbone of modern software development. They enable applications and systems to communicate and share data efficiently.</mark> 

**<mark>Booking</mark>** Booking management is the process of organizing, tracking, **<mark>Management</mark>** and handling reservations or appointments efficiently across various platforms. 

**<mark>ISO/IEC 25010</mark>** <mark>The quality of a system is the degree to which the system satisfies the stated and implied needs of its various stakeholders, and thus provides value. Those stakeholders' needs (functionality, performance, security, maintainability, etc.) are precisely what is represented in the quality model, which categorizes the product quality into characteristics and sub-characteristics.</mark> 

**Manual Record-** The traditional method of documenting service requests **Keeping** through logbooks and paper files, which this study aims to replace with a digital system. **OneSignal** A notification service integrated into the platform to send automated SMS and email alerts regarding booking confirmations and service status updates. **<mark>Role-Based</mark>** <mark>Role-based access control (RBAC) is a model for authorizing</mark> **<mark>Access Control</mark>** <mark>end-user access to systems, applications and data based on a</mark> **<mark>(RBAC)</mark>** <mark>user’s predefined role.</mark> **<mark>Real-Time</mark>** <mark>A type of computer programming process in which the</mark> **<mark>Update</mark>** <mark>information is processed and received by the receiver immediately without any delay is known as Real-time update.</mark> **<mark>Three-Tier</mark>** <mark>A three-tier application architecture is a modular client-</mark> **<mark>Architecture</mark>** <mark>server architecture that consists of a presentation tier, an application tier and a data tier.</mark> 

23 

24 

## **3 TECHNICAL BACKGROUND** 

## **3.1 Software Development Requirements** 



<!-- Start of picture text -->
a<br>a : a<br>Tailwind CSS<br>ae<br><!-- End of picture text -->











<!-- Start of picture text -->
a<br>Gq ><br>Sq<br>| fmt]<br>ee<br><!-- End of picture text -->

28 



<!-- Start of picture text -->
be used in the admin<br>dashboard to display<br>service statistics, booking<br>trends, and technician<br>performance summaries.<br>Google Chrome/ Web  A web browser such as<br>Browser  Google Chrome will be<br>used to test, access, and<br>evaluate the functionality,<br>responsiveness, and<br>usability of the developed<br>web-based system<br><!-- End of picture text -->

_Table 3. Software Development Requirements_ 

Table 3 shows the name, logo, and description of the software that will be used to 

develop a good quality system. These software tools and technologies are essential in building a functional, responsive, and user-friendly web-based platform for appliance repair service scheduling and management. 

Each software component has a specific role in the system, ranging from frontend interface design and backend processing to database management and notification support. The selected software will help the researchers develop a reliable  platform that improves service coordination between customers, technicians, and administrators 



<!-- Start of picture text -->
esee<br>fil<br><!-- End of picture text -->

# ~~<u>-</u>~~ 

31 

## **3.2 Peopleware** 

### 3.3.1     Project Proponents 

The project proponents serve as the primary developers and researchers responsible for the entire lifecycle of the platform.  They are tasked with gathering requirements, designing the three-tier architecture, and implementing the system using various frontend and backend technologies. Their role includes conducting data gathering through surveys and interviews, as well as performing functional and security testing prior to full deployment. 

### 3.3.2     Technical Adviser 

The researcher’s adviser provides professional oversight and expert guidance throughout the development process.  The adviser reviews the concept paper and technical methodology to ensure the project is viable and meets the academic standards. This individual affixes their concurrence to the project’s technical interventions, ensuring the solution effectively addresses the identified problems in appliance repair management. 

### 3.3.3     Appliance Technicians 

` They are key stakeholders who act as both primary data sources and system users. During the research phase, they provide insights into industry inefficiencies, such as the risks of manual record-keeping and difficulties in routing and scheduling. As end-users, 

32 

they participate in prototype and beta testing to verify that the integrated portal improves their visibility and provides a reliable database record management system for their services. 

### 3.3.4     Clients and Community Residents 

Clients and Community Residents represent the segment of the public that will utilize the platform to access repair services. They are involved during the data-gathering stage to establish a baseline for customer repair satisfaction and again during the testing phase to evaluate the system’s usability. Their participation is critical for measuring success through metrics such as the User Satisfaction Grade and the User Interface and Experience Scale. 

## **3.3 Sources of Data** 

The data and references used in this study were gathered mainly from Google Scholar and Google search. These online tools helped the researchers find many related studies, journal articles, conference papers, and other academic materials about appliance repair systems, technician scheduling, and online service platforms. This method allowed for a wide and thorough review of existing solutions that are relevant to the development of the proposed HomeService Pro system. 

Google Scholar was especially useful because it provides free and accessible scholarly materials. It made it easier to collect reliable and up-to-date references without needing expensive subscriptions. By using both Google Scholar and regular Google search, 

33 

the proponents were able to gather enough factual information from credible sources to support the planning and development of the capstone project. 

34 

## **4 METHODOLOGY** 

## **4.1 Methods in Data Gathering** 

The methods in data gathering are used by the researchers to collect relevant information needed for the development of the proposed system. In this study, these methods are essential in identifying the existing problems in appliance repair services, understanding the needs of the users, and determining the appropriate features and functionalities of the system. 

This study will utilize both primary and secondary data gathering methods. Primary data will be collected through structured interviews administered via online questionnaires, while secondary data will be gathered from published research studies, academic journals, articles, and credible online sources related to appliance repair services and system development. 

In addition, the study will involve selected IT professionals who will evaluate the proposed system in terms of its technical feasibility, functionality, and overall system performance. Their feedback will help validate whether the system design and features are appropriate, effective, and aligned with current technological standards. 

The integration of responses from homeowners, appliance technicians, and IT professionals will provide a comprehensive understanding of both user requirements and technical considerations. These gathered data will serve as the foundation for defining 

35 

system requirements, organizing the product backlog under the Scrum methodology, and guiding the design development of the HomeService Pro system. 

### **4.1.1 Interview Method** 

The interview method will be conducted as a primary data gathering technique to collect relevant information from potential users of the system.  The researchers will conduct interviews within selected subdivisions in Tarlac City to better understand the current experiences, challenges, and needs of homeowners and appliance repair technicians regarding repair and maintenance services. 

The interviews will be administered using an online survey tool, specifically Google Forms. A structured questionnaire will be prepared to gather consistent and organized responses from participants. The questionnaire will include questions related to how respondents currently find appliance repair services, the common problems they encounter in scheduling and communication, and their preferences for digital service platforms. 

The use of Google Forms will allow the researchers to distribute the questionnaire efficiently, collect responses in real time, and automatically organize the data for analysis. Respondents will be the homeowners  within the subdivisions and technicians around Tarlac City to ensure that the collected data reflects the actual needs and experiences of the target users. 

36 

The data gathered from this method will be used to identify key issues in traditional appliance repair services and will serve as a basis for the design and development of the proposed HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and Maintenance Services. 

### **4.1.2 Internet Method** 

The internet method is used for the development of the proposed system by serving as the primary source of secondary data and technical references. The researchers utilized the internet to gather relevant information that supports the conceptualization, design, and development of “HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and Maintenance Services”. 

The researchers were able to access scholar articles, journals, conference papers, and existing studies through online resources related to appliance repair service systems. web-based scheduling platforms, and technician management applications. These materials provided valuable insights into how similar systems operate, as well as the common challenges encountered in traditional services processes, such as inefficient scheduling, lack of communication, and poor record history. 

37 

### **4.1.3 Observation** 

The observation method will be used to support and validate the data collected through interviews by examining current practices in appliance repair services. The researchers will conduct observations within the subdivisions in Tarlac City to understand how customers locate technicians, schedule services, and manage communication. 

The observation will rely on real-life scenarios, user experiences gathered from interviews, and analysis of existing service workflows. The researchers will focus on identifying common issues such as delays in scheduling, lack of real-time updates, poor communication, and difficulties in tracking service. 

### **4.1.4 Respondents of the Study** 

<mark>The respondents for HomeService Pro were selected based on the framework of existing studies in software quality and field service management, which emphasize a dualevaluation approach involving both technical experts and target end-users. The study involves IT Professionals to evaluate the platform’s technical architecture, security, and functional suitability. The user-based evaluation is further supported by respondents consisting of appliance repair technicians and homeowners (customers). This selection is based on the research principle that a system’s practical effectiveness must be validated by those directly involved in the service cycle. To gather this data, the researchers utilized Purposive Sampling for the selection of experts and technicians, a method widely supported in academic literature for studies requiring participants with specialized knowledge or professional backgrounds. Additionally, Convenience Sampling was applied</mark> 

38 

<mark>to the customer group, a technique common in existing capstone studies where accessibility and prior experience with the service industry are prioritized to ensure a feasible and relevant data collection process within the study's timeline.</mark> 

### **4.1.5 Research Instruments** 

The primary instrument for data collection in this study is a structured, adopted questionnaire based on the ISO/IEC 25010 Software Quality Model. This instrument was chosen because it is the industry standard in existing software engineering literature for evaluating the technical and operational quality of web-based systems. The questionnaire is designed to gather quantitative data through a 5-point Likert Scale, allowing respondents to rate their level of agreement with various performance statements. 

The research instrument is divided into two main parts. The first one gathers the demographic profile of the respondents, and the other one contains the system evaluation items categorized into four key characteristics: Functional Suitability, Usability, Efficiency, and Security. Each category consists of specific items aimed at determining if the features of "HomeService Pro" effectively address the identified problems in appliance repair management. 

|Scale|Range|Verbal Interpretation|
|---|---|---|
|5|4.51 -— 5.00|Strongly Agree (Excellent)|
|4|3.51 -—4.50|Agree (Very Satisfactory)|
|3|2.51 -— 3.50|Neutral (Satisfactory)|
|2|1.51 — 2.50|Disagree (Fair)|
|4|1.00—1.50|stronglyDisagree(Poor)|



40 

2.2 The registration and login process is straightforward for all users. 

2.3 The system provides clear notifications regarding booking status. 

3.. Efficiency 

3.1  The system responds quickly to user inputs and commands. 

3.2 The scheduling process takes less time compared to manual methods. 

3.3 The database retrieves technician records without significant delay. 

- 4.. Security 

4.1 User accounts and personal information are protected by secure login 

credentials. 

4.2 The system prevents unauthorized access to service records. 

4.3 Data transactions within the platform feel safe and reliable. 

### **4.1.6 Statistical Treatment** 

To ensure the accuracy and reliability of the data collected from the evaluation of 

HomeService Pro, the researchers will employ several statistical tools. These tools are based on established quantitative research methods used in existing studies to interpret 

Likert scale responses and demographic data. 



<!-- Start of picture text -->
P= f x 100<br>n<br><!-- End of picture text -->



<!-- Start of picture text -->
_ df: w)<br>x =<br>n<br><!-- End of picture text -->



<!-- Start of picture text -->
=_ dS; — xy<br>n— |<br><!-- End of picture text -->

43 

Where: 

s = Standard Deviation 

Xi= Individual score/response 

x̄ = Calculated Mean 

n = Total number of respondents 

### 4. Verbal Interpretation 

Following the analysis of the means, the results will be interpreted using a scale from 

1.00 to 5.00. This follows the standard interpretation table used in existing research to categorize the system's performance from "Poor" to "Excellent." 



<!-- Start of picture text -->
Log In x<br>Email<br>© Enter your email<br>Password<br>& Enter your password<br>Don't have an account? Sign up<br><!-- End of picture text -->



<!-- Start of picture text -->
Book a Service x<br>Where do you need service?<br>Enter your address or detect location<br>Service Location<br>© 123 Main Street, New York, NY 10001<br>J Use Current Location<br>Popular areas:<br>Manhattan, NY Brooklyn, NY Queens, NY Bronx, NY<br><!-- End of picture text -->



<!-- Start of picture text -->
Book a Service x<br>< Back<br>What appliance needs repair?<br>Select appliance and issue<br>Washing MachineSelected Appliance Change<br>What's the issue?<br>@ Won't spin<br>@ Water Not Draining<br>@ Leaking<br>@ Won't Start<br>@ Loud Noises<br>Find Technicians<br><!-- End of picture text -->



<!-- Start of picture text -->
Book a Service x<br>< Back<br>Available Technicians<br>123 Main Street, New York, NY 10001<br>@ Washing Machine - Water Not Draining<br>John Martinez Available<br>% 4.9 (234) © 08 miles<br>Refrigerators Dishwashers<br>Select John Martinez<br>e Sarah Chen Available<br>3 % 48 (189) © 12 miles<br>Washers Dryers<br>Select Sarah Chen<br>Mike Thompson Available<br>% 4.7(156) © 1.5 miles<br>All Appliances<br>Select Mike Thompson<br>@ Emily Rodriguez Bus,<br>HVAC Ovens<br><!-- End of picture text -->



<!-- Start of picture text -->
Book a Service x<br>< Back<br>WashingMike ThompsonMachine - Water Not Draining e<br>Distance<br>1.5 miles<br>© ETA: 12 minutes<br>® Your Location<br>Hi! I'm Mike Thompson. I'll be handling your Washing Machine - Water Not<br>Draining. I'm on my way!<br>5:17 PM<br>ETA: 15 minutes. You can track my location on the map above.<br>5:18 PM<br>Type a message...<br><!-- End of picture text -->



<!-- Start of picture text -->
HomeService Pro: A Scheduling and<br>Management Platform for Home<br>Appliance Repairs and<br>Maintenance Services<br>Creating User<br>AccountLogin [et<br>Schedule Uf<br>Apponnnest: Ser (Repairmen)<br>Accept and Take<br>Appointment<br>CO Undertake Repair<br>User (Client eleS ManageView Repair Profiles and /<br>Transaction History<br>Browse and View<br>the Platform<br>Handle User<br>Manage System -<br>and Users Admin<br>Track Repairmen<br>and Ongoing<br>Repair<br><!-- End of picture text -->

50 

Figure 7 shows the use case diagram of the developed system, the diagram shows and visualizes the actors of the system, such as users, repairmen and client, and the administrator and their respective functions and roles. 

### **4.3.1.2 User Characteristics** 

The desired outcome of the system is to enhance independent repairmen availability and visibility, implement faster transactions, scheduling  and improve overall customer service and satisfaction 

The user (client) will be able to: 

1. Create and Manage Account 

2. Login 

3. Schedule Appointments 

4. Browse the Platform and Track Repairmen 

5. View Available Repairmen 

6. View Transaction and Repair History 

7. Logout 

The user (repairmen) will be able to: 

1. Create and Manage Account 

   2. Login 

   3. Accept and Take Appointment 

   4. Undertake Repair 

   5. Browse and View the Platform 

   6. View Repair and Transaction History 

51 

The administrator will be able to: 

1. Handle User Transactions 

2. Manage Users’ Appointments and Schedule 

3. Manage System 

4. System Maintenance and Upkeep 

5. Manage User Data 

### **4.3.1.3 Functional Requirements** 

This chapter outlines all the necessary functional requirements of the system, which 

includes the user experience and features,  it includes the priority and complexity of implementation of the requirements to evaluate its difficulty. 

_Table 5. Functional Requirement for the user_ 

|**Req.ID**|**Requirement Description**|**Priority**|**Complexity**|
|---|---|---|---|
|FR1|The user shall be able to create an account with the<br>username andpassword they provided|High|Medium|
|FR2|The user shall be able to login to the system using the<br>registered account|High|Medium|
|FR3|The user shall be able to schedule an appointment for a<br>repair|High|Medium|
|FR4|The user shall be able to view, select, and track repairmen,.<br>repair and status from theplatform|High|High|
|FR5|The user shall be able to view repair history and its<br>correspondingdetails|High|Medium|
|FR6|The user shall be able to accept and take an appointment<br>appropriatelyand includes necessarydetails for repairs|High|Medium|
|FR7|The user shall be able to detail their appliance issues and<br>problems which are used for further diagnosis of repair|High|High|
|FR8|The user shall be able to update and modifytheirprofile|High|High|



52 

### **4.3.1.4 Non-Functional Requirements** 

This Chapter provides the non-functional requirements pertaining to the safety and 

security of the system, which are necessary to protect and secure user data and implements 

strict authorization and access control. 

_Table 6. Non-Functional Requirement for Safety and Security of HomeService Pro: A Scheduling_ 

_and Management Platform for Home Appliance Repairs and Maintenance Services_ 

|Code|Dependencies Description|
|---|---|
|SS1|The system shall require a username andpassword to access the webportal|
|SS2|The system shall implement a Role-Based Access Control to improve<br>authentication, requiring permissions and authorization for different roles within<br>the system|
|SS3|All user data shall utilize encryption and security mechanisms against<br>vulnerabilities and threats|





<!-- Start of picture text -->
[ot — |__cteaoptencen<br>Details<br>‘Status Detailed_Report Name<br>Details" € FK| Ticket_ID Tyba<br>Fk | Schedule_ID Make<br>Year<br>Details<br>Issue_ID<br>RepairSchedule<br>ss je<br>Name<br>Status<br>d FK | Repairman1D ae | bs Address_Line2e<br>FK| ClientProfile_ID FK| ClientAppliances_ID =<br>Re User_Repairman | C_password C_usemame FK|FK| ClientContact_IDClientadd_ID City_MnProvinceno<br>User_RepairmanProfile RepairmanProfile_1D Zip_Code<br>ae —<br>Name R_password ClientContact<br>bx ae<br>Mos jx[o Pao<br>TelNo Client_ID b> Sec_MobileNo<br>Availabilty q Repairman_ID Sec_TeiNo<br>Details Repai_IDSpar<br>Reviews Repairman_Background Schedule1D<br>RepairmanBG_ID mje rene<br>Skills ClientAppliances_ID_<br>Education Date<br>Certifications Status<br>Assessment<br>Ratings<br><!-- End of picture text -->



<!-- Start of picture text -->
Pro<br>os os<br><!-- End of picture text -->

55 

### **4.3.2.3 Operating Environment** 

This chapter describes the operating environment the researchers will use for the developing, documenting and testing of the HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and Maintenance Services. 

_Table 7. Operating Environment of  HomeService Pro: A Scheduling and Management Platform_ 

_for Home Appliance Repairs and Maintenance Services_ 

|Code|Environment Description|
|---|---|
|OE1|The computer shall use Windows 10 or higher operating system in<br>development and testing|
|OE2|The computer shall have Visual Studio Code installed for<br>development|
|OE3|The computer shall use the latest version of web browser|
|OE4|The androidphone shall be runningon Android 10 or higher|



### **4.3.2.4 Design and Implementation Constraints** 

This section details the Design and Implementation Constraints during the 

development of  HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and Maintenance Services. 

_Table 8.  Design and Implementation Constraints of HomeService Pro: A Scheduling and_ 

_Management Platform for Home Appliance Repairs and Maintenance Services_ 

|Code|Design Constraints and Implementation Constraints Description|
|---|---|
|DC1|The system shall be developed usingweb frameworks|
|DC2|The system shall be developed with APIs for features|
|IC1|The system shall have a dedicated server to store data securely and<br>reliably|
|IC2|The system database shall be developed usingMySQL|



56 

### **4.3.3 System Development Methodology** 

<mark>For the development of HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and Maintenance Services, the researchers utilize the Agile Software Development Methodology.</mark> 

<mark>The decision to use Agile came from the need to be flexible and make improvements along the way, based on feedback from users. Since many people are involved in the system such as customers, technicians, and administrators, we knew that the requirements would likely change as we developed it. Agile lets us deliver features a little at a time, so we can test, validate, and refine them early on. It ensures the system stays in line with what users need throughout the entire development process. With Agile, we can make adjustments as we go, which helps us create a system that really works for everyone involved.</mark> 

### **4.3.3.1 Planning** 

During the planning phase, the researchers developed the proposed system, the functions, features, the goals and processes within, the requirements, the outlook of implementation and development of the system, and assigning specific tasks to each member. 

### **4.3.3.2 Scrum** 

57 

<mark>The development of HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and Maintenance Services requires a software development methodology that is both flexible and user focused. Consequently, the Scrum framework within Agile methodology is chosen as the main approach for development. Scrum is selected because of its effectiveness in handling intricate and changing project requirements. The intended system consists of various interacting elements, including scheduling, technician assignments, customer management, and real-time notifications. These functionalities need ongoing refinement and validation, making a strict, linear approach less appropriate. Scrum, with its iterative and incremental framework, enables the researchers to produce functional parts of the system in short cycles referred to as sprints, thus ensuring ongoing progress and enhancement.</mark> 



<!-- Start of picture text -->
HomeService Pro Development Burndown Chart<br>120<br>100<br>—~ 80<br>2<br>c=<br>> 60<br>e<br>©<br>& 40<br>=<br>S$ 20<br>©<br>£ &<br>3 Feb 1 Feb 15th March 1 March 15 April ist April 15 May 1st<br>oO<br>a Time Period<br>—2e— Ideal Burndown —— Actual Progress<br><!-- End of picture text -->

59 

## **4.4 To evaluate the performance of HomeService Pro: A Scheduling and Management Platform for Home Appliance Repairs and** 

## **Maintenance Services** 

**4.4.1** ISO/IEC 25010 Software Quality Evaluation 

_Table 9. IT Expert Evaluation for System Functionality_ 

|Criteria|5|4|3|2|1|
|---|---|---|---|---|---|
|The system provides a scheduling and<br>booking process for appliance repair and<br>maintenance services.||||||
|The system effectively manages service<br>requests, including booking confirmation,<br>updates, and completion.||||||
|The system provides accurate tracking and<br>monitoring of service requests from booking<br>to completion.||||||
|The system includes a feedback and rating<br>to evaluate the quality of service provided<br>by the client.||||||



60 

_Table 10. IT Expert Evaluation Criteria for User Interface_ 

|Criteria|5|4|3|2|1|
|---|---|---|---|---|---|
|The system interface is based on principles<br>of UI design like consistency, left alignment<br>and grid layout||||||
|The system participates core components<br>like login, signup, registration, location<br>interface and chat interface||||||
|The system ensures consistency of design||||||
|The system offers easy navigation interface<br>in terms of user-friendlines||||||



_Table 11. IT Expert Evaluation Criteria for Reliability_ 

|Criteria|5|4|3|2|1|
|---|---|---|---|---|---|
|The system continues to function<br>appropriately under normal operating<br>conditions without crashing, even when an<br>unexpected error||||||
|The technician finder function load properly<br>without any error during usage||||||
|The system stores and retrieves data<br>accurately||||||
|The chat are having no delays and errors<br>while being used by the client and<br>technician||||||
|The live location works without any errors<br>during the chat interaction||||||



61 

_Table 12. End User Evaluation Criteria for Performance Efficiency_ 

|Criteria|5|4|3|2|1|
|---|---|---|---|---|---|
|The system provides fast response time<br>while accessing the core features||||||
|The location will be used to show the<br>nearby technician, and the system will load<br>smoothly||||||
|The system process user inputs quickly and<br>accurately||||||
|The system efficiently handles real-time<br>updates like availability status||||||
|The system performs well under repeated<br>usage without unexpected crashes||||||
|The system optimizes resource usage for<br>smooth operation between the client and the<br>technician||||||



62 

_Table 13. End User Evaluation Criteria for Interaction Capability_ 

|Criteria|5|4|3|2|1|
|---|---|---|---|---|---|
|The system allows easy interaction thru the<br>features like map, technician locator, and<br>chat interaction||||||
|The system is responsive to user actions||||||
|The system enable to provide clear feedback<br>schedule confirmation, chat notifications,<br>and technician notifications||||||
|The system allows a smooth navigation<br>across different feature and pages||||||
|The system allows users to choose and see<br>details of the technician via profile||||||
|The system allows user to interact<br>efficiently with or without having to take<br>complicated steps||||||
|The system offers a full interactivity with a<br>user friendly interface||||||



63 

_Table 14. IT Expert Evaluation Criteria for Security_ 

|Criteria|5|4|3|2|1|
|---|---|---|---|---|---|
|The system protects record and function<br>form unauthorized access during normal<br>usage||||||
|The system effectively prevents<br>unauthorized access to sensitive data<br>through proper access control mechanism||||||
|The system verifies the identification of the<br>technician before granting permission to<br>register to the system||||||
|Personal data appears to be safe within the<br>system||||||
|User accounts are protected through secure<br>login credentials||||||





<!-- Start of picture text -->
TestCase ID | Module Feature Test Scenario Input Data Expected Output Result<br>Tet (ser Registration Registerwith valid information Compname, va id l  email, passwordete Account successfully created<br>TC-02 (ser Registration Registerwith missing fields Blank email password Errormessage displayed<br>TC-03 (ser Login Login with valid credential Registered email and password (ser redirectedto dashboard<br>TC-O4 (ser Login Login with invalid credential Wrong email/password Invallog n i  message displayed<br>TC Technician Provile Technician updates profile Name, skills, availability Provile updated successfully<br>TC-6 Booking System Book repair service with complete details Anpliance type, date, location Booking request submitted<br>Tt Booking System  Bookservcewith incomplete details Missing datelocation Warning message displayed<br>TC-08 Technician Matching Searnearby te hnician c h User location Available technicians cisplayed<br>TC-08 live Tracking Track technician location Active booking request Technician location shown on map<br>TC-l0 Chat System Send massage to technician Hello, where are you? Messagesent and received<br>Tell Notification System Booking contirmation alert Successtul booking SMS | website notificationreceived<br>C12 Feedback System — Submit technician rating 5 stars with comment Rating saved successfully<br>C3 Admin Panel View booking records Admin login Booking records displayed<br>TC-l4 Logout User logs out account Click logout button \ser rgrectedto login gage<br><!-- End of picture text -->

|TestCaseID|| Module Feature|TestScenario|Input Data|ExpectedOutput|Result|
|---|---|---|---|---|---|
|Tet|(ser Registration|Register<br>withvalidinformation|Comp**l**ete<br>name, va idemail, password|Accountsuccessfullycreated||
|TC-02|(ser Registration|Register<br>withmissingfields|Blank email password|Errormessage displayed||
|TC-03|(serLogin|Login with valid credential|Registered emailand password|(serredirected<br>todashboard||
|TC-O4|(ser Login|Loginwithinvalidcredential|Wrongemail/password|Inval**i**<br>log nmessagedisplayed||
|TC|Technician Provile|Technician updates profile|Name,skills,availability|Provile updated successfully||
|TC-6|Booking System|Bookrepairservicewithcomplete details|Anpliancetype,date,location|Bookingrequestsubmitted||
|Tt|BookingSystem|Bookservce<br>withincomplete details|Missing datelocation|Warning message displayed||
|TC-08|Technician Matching|Sear**c**h<br>nearby te hnician|User location|Availabletechnicians cisplayed||
|TC-08|liveTracking|Track technician location|Activebookingrequest|Technicianlocationshownonmap||
|TC-l0|ChatSystem|Send massage totechnician|Hello, where areyou?|Message<br>sent andreceived||
|Tell|NotificationSystem|Booking contirmation alert|Successtul booking|SMS |websitenotification<br>received||
|C12|Feedback System —|Submit technician rating|5 starswith comment|Ratingsavedsuccessfully||
|C3|AdminPanel|Viewbookingrecords|Adminlogin|Booking records displayed||
|TC-l4|Logout|Userlogsoutaccount|Clicklogoutbutton|\serrgrected<br>tologingage||



65 

## **APPENDICES** 

## **Appendix A: First Technical Adviser Capstone Consultation** 



66 

### **Appendix B: Subject Teacher Signature of Invitation** 



**Appendix C: Final Revision Consultation of Technical Adviser** 



<!-- Start of picture text -->
X ee npetve OE<br>ie wnt 1 ge ate EE EE SASS ATP<br>eu NT ak a ae on<br>— eyeres nines ee<br>38ed —a.FSSA~ antWFoumaneAMAR EPRPr a tes.tSN SOTLaandSee He EEi AeRES<br>eee eee<br>ie ; aN SSS=S—<br>. SSS<br>> =e => > ~<br>a es ——<br>a - ae<br>~,<br><!-- End of picture text -->

68 

## **REFERENCES** 

**Alvarado Baudat, M. G., Vertiz Asmat, C., & Sierra-Liñan, F. (2025).** Mobile application based on geolocation for the recruitment of general services in Trujillo, La Libertad. _International Journal of Advanced Computer Science & Applications, 16_ (2). https://doi.org/10.14569/IJACSA.2025.01602XXXX 

**Benhadi, R., & Ghouali, S. (2023).** Smart solutions for smartphone repair: Connecting users with expert technicians. In _International Conference on Artificial Intelligence in Renewable Energetic Systems_ (pp. 312–320). Springer Nature Switzerland. https://doi.org/10.1007/978-3-031-60632-8_26 

**Chowdhury, M., Faisal, A., & Chowdhury, S. (2025).** Think Tank: A home appliance and vehicle repair assistance smartphone application with technician hiring and repair schedule booking features. In _2025 4th OPJU International Technology Conference (OTCON) on Smart Computing for Innovation and Advancement in Industry 5.0_ (pp. 1–6). IEEE. https://doi.org/10.1109/OTCON.2025.11070814 

**Deshmukh, A., Deshpande, A., & Jadhav, R. (n.d.).** Request management system for electronic gadgets and appliances 

69 

**May, T. P., & Aman, H. (2021).** A development of a management information system for 

home appliance repairing. _Applied Information Technology and Computer Science, 2_ (2),1650–1662. 

https://publisher.uthm.edu.my/periodicals/index.php/aitcs/article/view/2407 

**Muhamad, A. N., Zaini, N. S., & Thin, S. A. A. S. S. (2024).** MyService: Modernizing appliance service management. _National EngiTech Digest, 1_ (1). https://jktss.puo.edu.my/jurnalpuo/index.php/NED/article/view/142 

**Nasir, M., Rajkumari, Y., & Adil, M. (2024).** After-sales service and brand reputation: A case of kitchen appliance industry. _International Journal of Quality and Service Sciences, 16_ (3), 413–431. https://doi.org/10.1108/IJQSS-1237866 

**Nunes, C., & Lopes, M. P. (2022).** Technician routing and scheduling problem: A case study. In _International Conference on Quality Innovation and Sustainability_ (pp. 399– 408). Springer. https://doi.org/10.1007/978-3-031-12914-8_30 

**Ruaya, P. (2023).** Development and assessment of online local home services application system for maintenance and repairs. _International Research Journal of Advanced EngineeringandScience,8_ 136–139. http://irjaes.com/wp- 

content/uploads/2023/05/IRJAES-V8N2P195Y23.pdf 

70 

**Suh, Y. (2025).** Discovering customer segments through interaction behaviors for home appliancebusiness _JournalofBigData,12_ (1),Article57. https://doi.org/10.1186/s40537-02501111-y 

**Tomić, T., Lavrnić, I., & Viduka, D. (2025).** The impact of website design on customer satisfaction and purchase intention. _Journal of Process Management and New Technologies,13_ (1-2),1–13. 

https://aseestant.ceon.rs/index.php/jouproman/article/view/51904 

**Ruaya, P. (2023).** Development and Assessment of Online Local Home Services Application System for Maintenance and Repairs. _International Research Journal of Advanced Engineering and Science_ , _8_ , 136-139. 

**Katukam, S., Kanukula, A., Shashank, A., Lokesh, K., & Rajitha, A. (2024, December).** Streamlined On-Demand Home Services: Connecting Customers with Expert Providers Through a Web Application. In _International Conference on Data Science and Management_ (pp. 247-256). Singapore: Springer Nature Singapore. 

**May, T. P., & Aman, H. (2021).** A Development of a Management Information System for Home Appliance Repairing. _Applied Information Technology And Computer Science_ , _2_ (2), 1650-1662. 

**Saundariya, K., Abirami, M., Senthil, K. R., Prabakaran, D., Srimathi, B., & Nagarajan, G. (2021, May).** Webapp service for booking handyman using mongodb, 

71 

express JS, react JS, node JS. In _2021 3rd International Conference on Signal Processing and Communication (ICPSC)_ (pp. 180-183). IEEE. 

**Aji, A., Thankachan, A., Varkey, D., Nair, H., Thomas, K. A., & VR, R. (2022).** Quick Reacher-An Application for Home Service. _International Journal for Research in Applied Science and Engineering Technology (IJRASET)_ . 

**Donaldo, E., Maxian, K. L., Pilongo, E. J., Ramos, G. L. T., Sazon, V. K. T., & Monta, S. N. M. (2025).** Information Systems Development Plan for JQ Aircon and Refrigeration Services. 

