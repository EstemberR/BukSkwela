1:1:<!DOCTYPE html>
2:2:<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
3:3:<head>
4:4:    <meta charset="utf-8">
5:5:    <meta name="viewport" content="width=device-width, initial-scale=1">
6:6:    <meta name="csrf-token" content="{{ csrf_token() }}">
7:7:    <title>@yield('title') - {{ tenant('id') }}</title>
8:8:    <!-- jQuery first -->
9:9:    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
10:10:    <!-- Bootstrap CSS -->
11:11:    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
12:12:    <!-- Font Awesome -->
13:13:    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
14:14:    <!-- Alpine.js -->
15:15:    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
16:16:    <style>
17:17:        /* Reset and base styles */
18:18:        * {
19:19:            margin: 0;
20:20:            padding: 0;
21:21:            box-sizing: border-box;
22:22:        }
23:23:
24:24:        /* Dark Mode Variables */
25:25:        :root {
26:26:            /* Light mode variables */
27:27:            --bg-color: #f3f4f6;
28:28:            --text-color: #111827;
29:29:            --text-muted: #6B7280;
30:30:            --border-color: #e5e7eb;
31:31:            --card-bg: #ffffff;
32:32:            --sidebar-bg: #ffffff;
33:33:            --navbar-bg: #ffffff;
34:34:            --primary-color: rgb(3, 1, 43);
35:35:            --primary-hover: rgb(10, 8, 70);
36:36:            --accent-color: rgb(213, 147, 4);
37:37:            --shadow-color: rgba(0, 0, 0, 0.1);
38:38:            --input-bg: #ffffff;
39:39:            --input-border: #e5e7eb;
40:40:            --dropdown-bg: #ffffff;
41:41:            --hover-bg: rgba(243, 244, 246, 0.8);
42:42:            --logo-filter: none;
43:43:            --avatar-border: #e5e7eb;
44:44:            --modal-bg: #ffffff;
45:45:            --badge-bg: rgba(3, 1, 43, 0.1);
46:46:            --badge-color: rgb(3, 1, 43);
47:47:            --sidebar-icon-color: rgb(0, 0, 0);
48:48:            --sidebar-text-color: rgb(0, 0, 0);
49:49:            --buk-text-color: rgb(213, 147, 4);
50:50:            --buk-only-color: rgb(213, 147, 4);
51:51:        }
52:52:        
53:53:        /* Dark mode class applied to body */
54:54:        body.dark-mode {
55:55:            --bg-color: #111827;
56:56:            --text-color: #f3f4f6;
57:57:            --text-muted: #9CA3AF;
58:58:            --border-color: #374151;
59:59:            --card-bg: #1F2937;
60:60:            --sidebar-bg: #1F2937;
61:61:            --navbar-bg: #1F2937;
62:62:            --primary-color: rgb(59, 130, 246);
63:63:            --primary-hover: rgb(96, 165, 250);
64:64:            --accent-color: rgb(251, 191, 36);
65:65:            --shadow-color: rgba(0, 0, 0, 0.3);
66:66:            --input-bg: #374151;
67:67:            --input-border: #4B5563;
68:68:            --dropdown-bg: #1F2937;
69:69:            --hover-bg: rgba(55, 65, 81, 0.8);
70:70:            --logo-filter: brightness(1.5) contrast(1.2);
71:71:            --avatar-border: #4B5563;
72:72:            --modal-bg: #1F2937;
73:73:            --badge-bg: rgba(59, 130, 246, 0.2);
74:74:            --badge-color: rgb(96, 165, 250);
75:75:            --sidebar-icon-color: #a0aec0;
76:76:            --sidebar-text-color: #e2e8f0;
77:77:            --buk-text-color: rgb(251, 191, 36);
78:78:            --buk-only-color: #ffffff;
79:79:            --dropdown-active-bg: rgb(59, 130, 246);
80:80:            --dropdown-active-text: #ffffff;
81:81:        }
82:82:        
83:83:        /* Apply variables to elements */
84:84:        body {
85:85:            background: var(--bg-color);
86:86:            color: var(--text-color);
87:87:            max-width: 100vw;
88:88:            transition: all 0.3s ease;
89:89:        }
90:90:        
91:91:        /* Specific dark mode overrides with higher specificity */
92:92:        body.dark-mode {
93:93:            background-color: var(--bg-color) !important;
94:94:            color: var(--text-color) !important;
95:95:        }
96:96:        
97:97:        body.dark-mode .sidebar {
98:98:            background-color: var(--sidebar-bg) !important;
99:99:            border-right-color: var(--border-color) !important;
100:100:        }
101:101:        
102:102:        body.dark-mode .top-navbar {
103:103:            background-color: var(--navbar-bg) !important;
104:104:            border-bottom-color: var(--border-color) !important;
105:105:        }
106:106:        
107:107:        body.dark-mode .main-content {
108:108:            background-color: var(--bg-color) !important;
109:109:        }
110:110:        
111:111:        body.dark-mode .dropdown-menu {
112:112:            background-color: var(--dropdown-bg) !important;
113:113:            border-color: var(--border-color) !important;
114:114:        }
115:115:        
116:116:        body.dark-mode .dropdown-item {
117:117:            color: var(--text-color) !important;
118:118:        }
119:119:        
120:120:        body.dark-mode .dropdown-item:hover {
121:121:            background-color: var(--hover-bg) !important;
122:122:        }
123:123:        
124:124:        /* Regular styles with CSS variables */
125:125:        .sidebar {
126:126:            position: fixed;
127:127:            top: 0;
128:128:            left: 0;
129:129:            bottom: 0;
130:130:            width: 250px;
131:131:            background: var(--sidebar-bg);
132:132:            border-right: 2px solid var(--border-color);
133:133:            box-shadow: 4px 0 10px var(--shadow-color);
134:134:            border-top-right-radius: 1.5rem;
135:135:            border-bottom-right-radius: 1.5rem;
136:136:            display: flex;
137:137:            flex-direction: column;
138:138:            z-index: 1000;
139:139:            overflow: hidden;
140:140:            transition: all 0.3s ease;
141:141:        }
142:142:        
143:143:        /* Compact mode sidebar */
144:144:        body.compact-sidebar .sidebar {
145:145:            width: 70px;
146:146:            overflow: visible;
147:147:            z-index: 1060;
148:148:        }
149:149:        
150:150:        body.compact-sidebar .sidebar:hover {
151:151:            width: 250px;
152:152:            z-index: 1060;
153:153:        }
154:154:        
155:155:        body.compact-sidebar .content-wrapper {
156:156:            margin-left: 70px;
157:157:            max-width: calc(100% - 70px);
158:158:        }
159:159:        
160:160:        /* Fix icon alignment in compact mode */
161:161:        body.compact-sidebar .sidebar .nav-item {
162:162:            display: flex;
163:163:            align-items: center;
164:164:            justify-content: center;
165:165:            height: 46px; /* Consistent height for all nav items */
166:166:            position: relative;
167:167:        }
168:168:        
169:169:        body.compact-sidebar .sidebar .nav-link {
170:170:            display: flex;
171:171:            align-items: center;
172:172:            justify-content: flex-start;
173:173:            padding: 0.75rem;
174:174:            width: 100%;
175:175:            height: 100%;
176:176:            position: relative;
177:177:        }
178:178:        
179:179:        body.compact-sidebar .sidebar:hover .nav-link {
180:180:            justify-content: flex-start;
181:181:            padding: 0.75rem 1rem;
182:182:        }
183:183:        
184:184:        body.compact-sidebar .sidebar .nav-link i {
185:185:            display: flex;
186:186:            align-items: center;
187:187:            justify-content: center;
188:188:            width: 24px;
189:189:            height: 24px;
190:190:            margin: 0;
191:191:            font-size: 1.25rem;
192:192:            line-height: 1;
193:193:            position: absolute;
194:194:            left: 23px; /* Fixed position for icons */
195:195:            transform: translateX(-50%); /* Center the icon */
196:196:        }
197:197:        
198:198:        body.compact-sidebar .sidebar:hover .nav-link i {
199:199:            margin-right: 0;
200:200:            position: absolute;
201:201:            left: 20px; /* Fixed position when hovered */
202:202:            transform: translateX(0); /* No centering needed when hovered */
203:203:        }
204:204:        
205:205:        body.compact-sidebar .sidebar .nav-link span,
206:206:        body.compact-sidebar .sidebar .tenant-name,
207:207:        body.compact-sidebar .sidebar .upgrade-btn span {
208:208:            opacity: 0;
209:209:            transform: translateX(10px);
210:210:            transition: all 0.2s ease;
211:211:            white-space: nowrap;
212:212:            display: inline-block;
213:213:            line-height: 1;
214:214:            margin-left: 35px; /* Fixed distance from the left edge */
215:215:        }
216:216:        
217:217:        body.compact-sidebar .sidebar:hover .nav-link span,
218:218:        body.compact-sidebar .sidebar:hover .tenant-name,
219:219:        body.compact-sidebar .sidebar:hover .upgrade-btn span {
220:220:            opacity: 1;
221:221:            transform: translateX(0);
222:222:        }
223:223:        
224:224:        /* Adjust dropdown items */
225:225:        body.compact-sidebar .sidebar .nav-item.dropdown {
226:226:            height: 46px;
227:227:        }
228:228:        
229:229:        body.compact-sidebar .sidebar .nav-item.dropdown .nav-link.dropdown-toggle {
230:230:            justify-content: center;
231:231:            padding: 0;
232:232:            width: 100%;
233:233:            height: 100%;
234:234:            position: relative;
235:235:        }
236:236:        
237:237:        body.compact-sidebar .sidebar:hover .nav-item.dropdown .nav-link.dropdown-toggle {
238:238:            justify-content: space-between;
239:239:            padding: 0.75rem 1rem;
240:240:        }
241:241:        
242:242:        body.compact-sidebar .sidebar .nav-item.dropdown .nav-content {
243:243:            display: flex;
244:244:            align-items: center;
245:245:            justify-content: flex-start;
246:246:            height: 100%;
247:247:            width: 100%;
248:248:            position: relative;
249:249:        }
250:250:        
251:251:        body.compact-sidebar .sidebar .nav-item.dropdown .nav-content i {
252:252:            position: absolute;
253:253:            left: 23px;
254:254:            transform: translateX(-50%);
255:255:        }
256:256:        
257:257:        body.compact-sidebar .sidebar:hover .nav-item.dropdown .nav-content i {
258:258:            position: absolute;
259:259:            left: 20px;
260:260:            transform: translateX(0);
261:261:        }
262:262:        
263:263:        body.compact-sidebar .sidebar .nav-item.dropdown .nav-content span {
264:264:            margin-left: 35px;
265:265:        }
266:266:        
267:267:        body.compact-sidebar .sidebar:hover .nav-item.dropdown .nav-content {
268:268:            justify-content: flex-start;
269:269:        }
270:270:        
271:271:        body.compact-sidebar .sidebar .dropdown-icon {
272:272:            display: none;
273:273:            position: absolute;
274:274:            right: 15px;
275:275:        }
276:276:        
277:277:        body.compact-sidebar .sidebar:hover .dropdown-icon {
278:278:            display: inline-block;
279:279:        }
280:280:        
281:281:        /* Upgrade button and premium indicator */
282:282:        body.compact-sidebar .sidebar .upgrade-btn,
283:283:        body.compact-sidebar .sidebar .premium-indicator {
284:284:            display: none;
285:285:        }
286:286:        
287:287:        body.compact-sidebar .sidebar:hover .upgrade-btn,
288:288:        body.compact-sidebar .sidebar:hover .premium-indicator {
289:289:            display: none;
290:290:        }
291:291:        
292:292:        body.compact-sidebar .sidebar .upgrade-btn i,
293:293:        body.compact-sidebar .sidebar .premium-indicator i {
294:294:            display: none;
295:295:        }
296:296:        
297:297:        body.compact-sidebar .sidebar:hover .upgrade-btn i,
298:298:        body.compact-sidebar .sidebar:hover .premium-indicator i {
299:299:            display: none;
300:300:        }
301:301:        
302:302:        body.compact-sidebar .sidebar .upgrade-btn span,
303:303:        body.compact-sidebar .sidebar .premium-indicator span {
304:304:            display: none;
305:305:        }
306:306:        
307:307:        body.compact-sidebar .sidebar .logo-container {
308:308:            display: flex;
309:309:            justify-content: center;
310:310:            align-items: center;
311:311:            flex-direction: column;
312:312:            padding: 1rem;
313:313:            gap: 0.5rem;
314:314:        }
315:315:        
316:316:        body.compact-sidebar .sidebar .logo-container img {
317:317:            max-width: 40px;
318:318:            max-height: 40px;
319:319:            transition: all 0.3s ease;
320:320:        }
321:321:        
322:322:        body.compact-sidebar .sidebar:hover .logo-container img {
323:323:            max-width: 100%;
324:324:            max-height: 60px;
325:325:        }
326:326:        
327:327:        body.compact-sidebar .sidebar .nav-link {
328:328:            padding: 0.75rem;
329:329:            justify-content: center;
330:330:            transition: all 0.3s ease;
331:331:        }
332:332:        
333:333:        body.compact-sidebar .sidebar:hover .nav-link {
334:334:            padding: 0.75rem 1rem;
335:335:            justify-content: flex-start;
336:336:        }
337:337:        
338:338:        body.compact-sidebar .sidebar .nav-link i {
339:339:            margin-right: 0;
340:340:            width: 20px;
341:341:            font-size: 1.25rem;
342:342:            transition: all 0.3s ease;
343:343:        }
344:344:        
345:345:        body.compact-sidebar .sidebar:hover .nav-link i {
346:346:            margin-right: 0.75rem;
347:347:        }
348:348:        
349:349:        body.compact-sidebar .sidebar .dropdown-menu {
350:350:            left: 70px;
351:351:            top: 0;
352:352:        }
353:353:        
354:354:        body.compact-sidebar .sidebar:hover .dropdown-menu {
355:355:            left: 100%;
356:356:        }
357:357:        
358:358:        /* Fix for dropdown in compact mode */
359:359:        body.compact-sidebar .sidebar .nav-item.dropdown .nav-content {
360:360:            display: flex;
361:361:            align-items: center;
362:362:            justify-content: center;
363:363:            width: 100%;
364:364:        }
365:365:        
366:366:        body.compact-sidebar .sidebar:hover .nav-item.dropdown .nav-content {
367:367:            justify-content: flex-start;
368:368:        }
369:369:        
370:370:        body.compact-sidebar .sidebar .dropdown-icon {
371:371:            display: none;
372:372:        }
373:373:        
374:374:        body.compact-sidebar .sidebar:hover .dropdown-icon {
375:375:            display: inline-block;
376:376:        }
377:377:        
378:378:        /* Auto-compact for layout-compact */
379:379:        body.layout-compact {
380:380:            /* This will be toggled by JavaScript */
381:381:        }
382:382:        
383:383:        .sidebar .nav-link {
384:384:            color: var(--sidebar-text-color);
385:385:            transition: all 0.3s ease;
386:386:        }
387:387:        
388:388:        .sidebar .nav-link:hover {
389:389:            background: var(--primary-color);
390:390:            color: #ffffff;
391:391:        }
392:392:        
393:393:        .sidebar .nav-link.active {
394:394:            background: var(--primary-color);
395:395:            color: #ffffff;
396:396:        }
397:397:        
398:398:        .top-navbar {
399:399:            background: var(--navbar-bg);
400:400:            box-shadow: 0 2px 10px var(--shadow-color);
401:401:            border-bottom: 2px solid var(--border-color);
402:402:            transition: all 0.3s ease;
403:403:        }
404:404:        
405:405:        .main-content {
406:406:            background: var(--bg-color);
407:407:            transition: all 0.3s ease;
408:408:        }
409:409:        
410:410:        .card, .settings-card, .card-body {
411:411:            background-color: var(--card-bg) !important;
412:412:            border-color: var(--border-color) !important;
413:413:            color: var(--text-color);
414:414:            transition: all 0.3s ease;
415:415:        }
416:416:        
417:417:        .dropdown-menu {
418:418:            background-color: var(--dropdown-bg) !important;
419:419:            border-color: var(--border-color) !important;
420:420:            transition: all 0.3s ease;
421:421:        }
422:422:        
423:423:        .dropdown-item {
424:424:            color: var(--text-color) !important;
425:425:        }
426:426:        
427:427:        .dropdown-item:hover {
428:428:            background-color: var(--hover-bg) !important;
429:429:        }
430:430:        
431:431:        /* Table styles for dark mode */
432:432:        .table {
433:433:            color: var(--text-color) !important;
434:434:        }
435:435:        
436:436:        .table tbody tr {
437:437:            background-color: var(--card-bg) !important;
438:438:        }
439:439:        
440:440:        .table tbody tr:nth-of-type(odd) {
441:441:            background-color: var(--hover-bg) !important;
442:442:        }
443:443:        
444:444:        /* Form controls */
445:445:        .form-control, .form-select {
446:446:            background-color: var(--input-bg) !important;
447:447:            border-color: var(--input-border) !important;
448:448:            color: var(--text-color) !important;
449:449:            transition: all 0.3s ease;
450:450:        }
451:451:        
452:452:        .form-control:focus, .form-select:focus {
453:453:            border-color: var(--primary-color) !important;
454:454:            box-shadow: 0 0 0 0.25rem rgba(var(--primary-color), 0.25) !important;
455:455:        }
456:456:        
457:457:        /* Text colors */
458:458:        .text-muted {
459:459:            color: var(--text-muted) !important;
460:460:        }
461:461:        
462:462:        /* Enhance tenant name display */
463:463:        .tenant-name {
464:464:            font-size: 1.15rem !important;
465:465:            margin: 0.5rem 0 !important;
466:466:            color: var(--buk-text-color) !important;
467:467:            font-weight: 700 !important;
468:468:            text-transform: uppercase;
469:469:            letter-spacing: 0.5px;
470:470:            text-shadow: 0 1px 1px var(--shadow-color);
471:471:            display: block !important;
472:472:            visibility: visible !important;
473:473:            opacity: 1 !important;
474:474:            transition: all 0.3s ease;
475:475:        }
476:476:
477:477:        .tenant-buk {
478:478:            color: var(--buk-only-color) !important;
479:479:            transition: color 0.3s ease;
480:480:        }
481:481:
482:482:        /* Pagination Styles */
483:483:        .pagination {
484:484:            display: flex;
485:485:            list-style: none;
486:486:            padding: 0;
487:487:            margin: 1rem 0;
488:488:            gap: 0.25rem;
489:489:        }
490:490:
491:491:        .pagination li {
492:492:            display: inline-block;
493:493:        }
494:494:
495:495:        .pagination li a,
496:496:        .pagination li span {
497:497:            display: flex;
498:498:            align-items: center;
499:499:            justify-content: center;
500:500:            padding: 0.5rem 1rem;
501:501:            min-width: 2.5rem;
502:502:            border-radius: 0.375rem;
503:503:            text-decoration: none;
504:504:            background-color: #fff;
505:505:            border: 1px solid #e5e7eb;
506:506:            color: #374151;
507:507:            font-size: 0.875rem;
508:508:            transition: all 0.2s;
509:509:        }
510:510:
511:511:        .pagination li.active span {
512:512:            background-color: rgb(3, 1, 43);
513:513:            color: #fff;
514:514:            border-color: rgb(3, 1, 43);
515:515:        }
516:516:
517:517:        .pagination li a:hover {
518:518:            background-color: #f3f4f6;
519:519:            border-color: #e5e7eb;
520:520:        }
521:521:
522:522:        .pagination li.disabled span {
523:523:            background-color: #f9fafb;
524:524:            color: #9ca3af;
525:525:            cursor: not-allowed;
526:526:        }
527:527:
528:528:        html, body {
529:529:            height: 100%;
530:530:            width: 100%;
531:531:            overflow: hidden;
532:532:            position: fixed;
533:533:        }
534:534:
535:535:        body {
536:536:            background: #f3f4f6;
537:537:            max-width: 100vw;
538:538:        }
539:539:
540:540:        /* Layout wrapper */
541:541:        .layout-wrapper {
542:542:            display: flex;
543:543:            height: 100%;
544:544:            width: 100%;
545:545:            overflow: hidden;
546:546:            position: relative;
547:547:        }
548:548:        
549:549:        /* Sidebar styles */
550:550:        .sidebar {
551:551:            position: fixed;
552:552:            top: 0;
553:553:            left: 0;
554:554:            bottom: 0;
555:555:            width: 250px;
556:556:            background: #fff;
557:557:            border-right: 2px solid #e5edff;
558:558:            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
559:559:            border-top-right-radius: 1.5rem;
560:560:            border-bottom-right-radius: 1.5rem;
561:561:            display: flex;
562:562:            flex-direction: column;
563:563:            z-index: 1000;
564:564:            overflow: hidden;
565:565:        }
566:566:
567:567:        .sidebar-content {
568:568:            flex: 1;
569:569:            display: flex;
570:570:            flex-direction: column;
571:571:            padding: 0;
572:572:            overflow-y: auto;
573:573:            overflow-x: hidden;
574:574:            scrollbar-width: none; /* Firefox */
575:575:            -ms-overflow-style: none; /* IE and Edge */
576:576:            width: 100%;
577:577:        }
578:578:        
579:579:        .sidebar-content::-webkit-scrollbar {
580:580:            display: none; /* Chrome, Safari, Opera */
581:581:        }
582:582:
583:583:        .nav.flex-column {
584:584:            width: 100%;
585:585:            padding: 0 1rem;
586:586:        }
587:587:
588:588:        .sidebar .nav-link {
589:589:            color: rgb(0, 0, 0);
590:590:            padding: 0.75rem 1rem;
591:591:            margin: 0.25rem 0;
592:592:            border-radius: 0.5rem;
593:593:            transition: all 0.3s ease;
594:594:            white-space: nowrap;
595:595:            width: 100%;
596:596:            display: flex;
597:597:            align-items: center;
598:598:            cursor: pointer;
599:599:            position: relative;
600:600:        }
601:601:
602:602:        .sidebar .nav-link:hover {
603:603:            background: rgb(3, 1, 43);
604:604:            color: #fff;
605:605:        }
606:606:
607:607:        .sidebar .nav-link.active {
608:608:            background: rgb(3, 1, 43);
609:609:            color: #fff;
610:610:        }
611:611:
612:612:        .sidebar .nav-link i {
613:613:            margin-right: 0.75rem;
614:614:            width: 20px;
615:615:            text-align: center;
616:616:            color: var(--sidebar-icon-color);
617:617:            transition: all 0.3s ease;
618:618:        }
619:619:
620:620:        .sidebar .nav-link:hover i,
621:621:        .sidebar .nav-link.active i {
622:622:            color: #ffffff;
623:623:        }
624:624:
625:625:        .sidebar .nav-link.dropdown-toggle::after {
626:626:            position: absolute;
627:627:            right: 1rem;
628:628:            top: 50%;
629:629:            transform: translateY(-50%);
630:630:        }
631:631:
632:632:        /* Main content wrapper */
633:633:        .content-wrapper {
634:634:            flex: 1;
635:635:            margin-left: 250px;
636:636:            height: 100%;
637:637:            display: flex;
638:638:            flex-direction: column;
639:639:            overflow: hidden;
640:640:            max-width: calc(100% - 250px);
641:641:            transition: all 0.3s ease;
642:642:        }
643:643:
644:644:        /* Top navbar styles */
645:645:        .top-navbar {
646:646:            flex-shrink: 0;
647:647:            background: #fff;
648:648:            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
649:649:            border-bottom: 2px solid #e5edff;
650:650:            z-index: 1050;
651:651:            height: 60px;
652:652:            display: flex;
653:653:            align-items: center;
654:654:            width: 100%;
655:655:            position: relative;
656:656:        }
657:657:
658:658:        .container-fluid {
659:659:            max-width: 100%;
660:660:            padding-left: 15px;
661:661:            padding-right: 15px;
662:662:        }
663:663:
664:664:        /* Main content styles */
665:665:        .main-content {
666:666:            flex: 1;
667:667:            padding: 1rem;
668:668:            background: #f3f4f6;
669:669:            overflow-y: auto;
670:670:            scrollbar-width: none; /* Firefox */
671:671:            -ms-overflow-style: none; /* IE and Edge */
672:672:        }
673:673:
674:674:        .main-content::-webkit-scrollbar {
675:675:            display: none; /* Chrome, Safari, Opera */
676:676:        }
677:677:
678:678:        /* User avatar styles */
679:679:        .user-avatar {
680:680:            width: 2.5rem;
681:681:            height: 2.5rem;
682:682:            border-radius: 0.5rem;
683:683:            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
684:684:        }
685:685:
686:686:        /* Upgrade button styles */
687:687:        .upgrade-btn {
688:688:            display: none;
689:689:        }
690:690:
691:691:        /* Dropdown menu styles */
692:692:        .dropdown-menu {
693:693:            border: none;
694:694:            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
695:695:            border-radius: 0.5rem;
696:696:            z-index: 1100;
697:697:            margin-top: 0;
698:698:            min-width: 220px;
699:699:            background: #fff;
700:700:            position: absolute;
701:701:            left: 100%;
702:702:            top: 0;
703:703:            transform: translateX(10px);
704:704:            margin-left: 0;
705:705:        }
706:706:
707:707:        .nav-item.dropdown {
708:708:            position: relative;
709:709:        }
710:710:
711:711:        .nav-item.dropdown .nav-link {
712:712:            display: flex;
713:713:            align-items: center;
714:714:            justify-content: space-between;
715:715:            width: 100%;
716:716:            text-align: left;
717:717:            border: none;
718:718:            background: none;
719:719:            padding: 0.75rem 1rem;
720:720:        }
721:721:
722:722:        .dropdown-item {
723:723:            padding: 0.75rem 1rem;
724:724:            color: #4b5563;
725:725:            transition: all 0.2s ease;
726:726:            cursor: pointer;
727:727:            display: flex;
728:728:            align-items: center;
729:729:            white-space: nowrap;
730:730:        }
731:731:
732:732:        .dropdown-item:hover {
733:733:            background: rgb(3, 1, 43);
734:734:            color: #fff;
735:735:        }
736:736:
737:737:        .dropdown-item i {
738:738:            width: 20px;
739:739:            text-align: center;
740:740:            color: var(--sidebar-icon-color);
741:741:            transition: all 0.3s ease;
742:742:        }
743:743:
744:744:        .dropdown-item:hover i {
745:745:            color: #ffffff;
746:746:        }
747:747:
748:748:        /* Mobile responsive */
749:749:        @media (max-width: 768px) {
750:750:            .sidebar {
751:751:                transform: translateX(-100%);
752:752:                transition: transform 0.3s ease;
753:753:            }
754:754:
755:755:            .sidebar.show {
756:756:                transform: translateX(0);
757:757:            }
758:758:
759:759:            .content-wrapper {
760:760:                margin-left: 0;
761:761:                max-width: 100%;
762:762:            }
763:763:
764:764:            .top-navbar {
765:765:                margin-left: 0;
766:766:            }
767:767:        }
768:768:
769:769:        /* Logo container styles */
770:770:        .logo-container {
771:771:            display: flex;
772:772:            justify-content: center;
773:773:            align-items: center;
774:774:            flex-direction: column;
775:775:            padding: 1rem;
776:776:            gap: 0.5rem;
777:777:        }
778:778:
779:779:        .logo-container img {
780:780:            max-width: 100%;
781:781:            height: auto;
782:782:            max-height: 60px;
783:783:        }
784:784:
785:785:        .logo-container h4 {
786:786:            font-size: 1rem;
787:787:            margin: 0;
788:788:            color: var(--buk-text-color);
789:789:            font-weight: 600;
790:790:        }
791:791:
792:792:        /* Navbar dropdown styles */
793:793:        .top-navbar .dropdown-menu {
794:794:            position: absolute;
795:795:            right: 0;
796:796:            left: auto;
797:797:            top: 100%;
798:798:            margin-top: 0.5rem;
799:799:            min-width: 200px;
800:800:            border: none;
801:801:            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
802:802:            border-radius: 0.5rem;
803:803:            z-index: 1060;
804:804:            background: #fff;
805:805:            transform: none !important;
806:806:        }
807:807:
808:808:        .top-navbar .dropdown {
809:809:            position: relative;
810:810:        }
811:811:
812:812:        .top-navbar .btn-link {
813:813:            color: #4b5563;
814:814:            text-decoration: none;
815:815:            padding: 0.5rem;
816:816:            border-radius: 0.5rem;
817:817:            transition: all 0.2s ease;
818:818:            display: flex;
819:819:            align-items: center;
820:820:        }
821:821:
822:822:        .top-navbar .btn-link:hover {
823:823:            background: rgba(0, 0, 0, 0.05);
824:824:        }
825:825:
826:826:        .top-navbar .dropdown-item {
827:827:            padding: 0.75rem 1rem;
828:828:            color: #4b5563;
829:829:            transition: all 0.2s ease;
830:830:            cursor: pointer;
831:831:            display: flex;
832:832:            align-items: center;
833:833:        }
834:834:
835:835:        .top-navbar .dropdown-item:hover {
836:836:            background: rgb(3, 1, 43);
837:837:            color: #fff;
838:838:        }
839:839:
840:840:        .top-navbar .dropdown-item i {
841:841:            width: 20px;
842:842:            text-align: center;
843:843:        }
844:844:
845:845:        /* Override Bootstrap's dropdown styles */
846:846:        .dropdown-menu[data-bs-popper] {
847:847:            top: 100%;
848:848:            left: auto;
849:849:            right: 0;
850:850:            margin-top: 0.5rem;
851:851:        }
852:852:
853:853:        .top-navbar .dropdown-menu-end {
854:854:            --bs-position: end;
855:855:        }
856:856:
857:857:        /* Ensure dropdowns are above other elements */
858:858:        .top-navbar .dropdown {
859:859:            z-index: 1060;
860:860:        }
861:861:
862:862:        .top-navbar .dropdown-menu.show {
863:863:            display: block !important;
864:864:        }
865:865:
866:866:        /* User avatar container */
867:867:        .user-avatar-container {
868:868:            width: 2.5rem;
869:869:            height: 2.5rem;
870:870:            border-radius: 0.5rem;
871:871:            overflow: hidden;
872:872:            cursor: pointer;
873:873:        }
874:874:
875:875:        .user-avatar-container img {
876:876:            width: 100%;
877:877:            height: 100%;
878:878:            object-fit: cover;
879:879:        }
880:880:
881:881:        /* Notification badge positioning */
882:882:        .notification-badge {
883:883:            position: absolute;
884:884:            top: 0;
885:885:            right: 0;
886:886:            transform: translate(25%, -25%);
887:887:        }
888:888:
889:889:        .navbar-nav .dropdown-menu {
890:890:            position: absolute;
891:891:        }
892:892:
893:893:        /* Sidebar dropdown styles */
894:894:        .sidebar .dropdown-menu {
895:895:            display: none;
896:896:            left: 100%;
897:897:            position: absolute;
898:898:            top: 0;
899:899:            margin-top: 0;
900:900:            margin-left: 0;
901:901:            border-radius: 0.5rem;
902:902:            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
903:903:            z-index: 1100;
904:904:            transition: none;
905:905:            min-width: 220px;
906:906:        }
907:907:        
908:908:        .sidebar .dropdown-menu.show {
909:909:            display: block;
910:910:        }
911:911:        
912:912:        .sidebar .nav-item.dropdown {
913:913:            position: relative;
914:914:        }
915:915:        
916:916:        .sidebar .nav-link.dropdown-toggle::after {
917:917:            display: none;
918:918:        }
919:919:        
920:920:        .sidebar .dropdown-item {
921:921:            padding: 0.75rem 1rem;
922:922:            color: #4b5563;
923:923:            display: flex;
924:924:            align-items: center;
925:925:        }
926:926:        
927:927:        .sidebar .dropdown-item:hover {
928:928:            background-color: var(--primary-color);
929:929:            color: #ffffff;
930:930:        }
931:931:        
932:932:        .sidebar .dropdown-item i {
933:933:            margin-right: 0.75rem;
934:934:            width: 20px;
935:935:            text-align: center;
936:936:            color: var(--sidebar-icon-color);
937:937:            transition: all 0.3s ease;
938:938:        }
939:939:        
940:940:        .sidebar .dropdown-item:hover i {
941:941:            color: #ffffff;
942:942:        }
943:943:        
944:944:        /* Update the nav-link styles for the Reports dropdown */
945:945:        .sidebar .nav-link.dropdown-toggle {
946:946:            display: flex;
947:947:            justify-content: space-between;
948:948:            align-items: center;
949:949:            padding-right: 2rem;
950:950:        }
951:951:        
952:952:        .sidebar .nav-link.dropdown-toggle .nav-content {
953:953:            display: flex;
954:954:            align-items: center;
955:955:        }
956:956:        
957:957:        .sidebar .nav-link.dropdown-toggle:focus {
958:958:            box-shadow: none;
959:959:        }
960:960:
961:961:        /* Remove hover effect specifically for Reports dropdown when on reports pages */
962:962:        body.reports-page .sidebar .nav-item.dropdown .nav-link.dropdown-toggle:hover {
963:963:            background: transparent;
964:964:            color: var(--sidebar-text-color);
965:965:        }
966:966:        
967:967:        body.reports-page .sidebar .nav-item.dropdown .nav-link.dropdown-toggle:hover i {
968:968:            color: var(--sidebar-icon-color);
969:969:        }
970:970:        
971:971:        /* Dark mode version */
972:972:        body.dark-mode.reports-page .sidebar .nav-item.dropdown .nav-link.dropdown-toggle:hover {
973:973:            background: transparent;
974:974:            color: var(--sidebar-text-color);
975:975:        }
976:976:        
977:977:        body.dark-mode.reports-page .sidebar .nav-item.dropdown .nav-link.dropdown-toggle:hover i {
978:978:            color: var(--sidebar-icon-color);
979:979:        }
980:980:
981:981:        /* Navbar specific dropdown styles */
982:982:        .navbar .dropdown-menu {
983:983:            position: absolute !important;
984:984:            inset: auto !important;
985:985:            right: 0 !important;
986:986:            left: auto !important;
987:987:            top: 100% !important;
988:988:            transform: none !important;
989:989:            margin-top: 0.5rem !important;
990:990:            min-width: 200px;
991:991:            border: none;
992:992:            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
993:993:            border-radius: 0.5rem;
994:994:            z-index: 1100;
995:995:            background: #fff;
996:996:        }
997:997:
998:998:        .navbar .dropdown {
999:999:            position: relative !important;
1000:1000:        }
1001:1001:
1002:1002:        /* Navbar buttons and controls */
1003:1003:        .navbar .btn-link {
1004:1004:            padding: 0.5rem;
1005:1005:            color: #4b5563;
1006:1006:            display: flex;
1007:1007:            align-items: center;
1008:1008:            justify-content: center;
1009:1009:            border-radius: 0.5rem;
1010:1010:            transition: all 0.2s ease;
1011:1011:            position: relative;
1012:1012:        }
1013:1013:
1014:1014:        .navbar .btn-link:hover {
1015:1015:            background: rgba(0, 0, 0, 0.05);
1016:1016:        }
1017:1017:
1018:1018:        .navbar .notification-badge {
1019:1019:            position: absolute;
1020:1020:            top: 0;
1021:1021:            right: 0;
1022:1022:            transform: translate(30%, -30%);
1023:1023:        }
1024:1024:
1025:1025:        /* User avatar styles */
1026:1026:        .navbar .user-avatar-container {
1027:1027:            width: 2.5rem;
1028:1028:            height: 2.5rem;
1029:1029:            border-radius: 0.5rem;
1030:1030:            overflow: hidden;
1031:1031:            cursor: pointer;
1032:1032:            transition: all 0.2s ease;
1033:1033:        }
1034:1034:
1035:1035:        .navbar .user-avatar-container:hover {
1036:1036:            opacity: 0.8;
1037:1037:        }
1038:1038:
1039:1039:        .navbar .user-avatar {
1040:1040:            width: 100%;
1041:1041:            height: 100%;
1042:1042:            object-fit: cover;
1043:1043:        }
1044:1044:
1045:1045:        /* Navbar dropdown items */
1046:1046:        .navbar .dropdown-item {
1047:1047:            padding: 0.75rem 1rem;
1048:1048:            display: flex;
1049:1049:            align-items: center;
1050:1050:            color: #4b5563;
1051:1051:            transition: all 0.2s ease;
1052:1052:        }
1053:1053:
1054:1054:        .navbar .dropdown-item:hover {
1055:1055:            background: rgb(3, 1, 43);
1056:1056:            color: #fff;
1057:1057:        }
1058:1058:
1059:1059:        .navbar .dropdown-item i {
1060:1060:            width: 20px;
1061:1061:            text-align: center;
1062:1062:            margin-right: 0.5rem;
1063:1063:        }
1064:1064:
1065:1065:        /* Navbar dropdown header */
1066:1066:        .navbar .dropdown-header {
1067:1067:            padding: 0.75rem 1rem;
1068:1068:            color: #4b5563;
1069:1069:            font-weight: 600;
1070:1070:            border-bottom: 1px solid #e5e7eb;
1071:1071:        }
1072:1072:
1073:1073:        /* Override Bootstrap's dropdown display */
1074:1074:        .navbar .dropdown-menu.show {
1075:1075:            display: block !important;
1076:1076:        }
1077:1077:
1078:1078:        .navbar .dropdown-toggle::after {
1079:1079:            display: none;
1080:1080:        }
1081:1081:
1082:1082:        /* Add these styles for the Free Trial indicator */
1083:1083:        .free-trial-indicator .badge {
1084:1084:            display: none;
1085:1085:        }
1086:1086:
1087:1087:        .free-trial-indicator .badge i {
1088:1088:            font-size: 0.75rem;
1089:1089:        }
1090:1090:
1091:1091:        /* Modal Styles */
1092:1092:        .subscription-modal {
1093:1093:            display: none !important;
1094:1094:        }
1095:1095:
1096:1096:        .subscription-overlay {
1097:1097:            position: fixed;
1098:1098:            top: 0;
1099:1099:            left: 0;
1100:1100:            width: 100%;
1101:1101:            height: 100%;
1102:1102:            background: rgba(0, 0, 0, 0.8);
1103:1103:            backdrop-filter: blur(5px);
1104:1104:        }
1105:1105:
1106:1106:        .subscription-content {
1107:1107:            position: fixed;
1108:1108:            top: 50%;
1109:1109:            left: 50%;
1110:1110:            transform: translate(-50%, -50%);
1111:1111:            z-index: 2001;
1112:1112:        }
1113:1113:
1114:1114:        /* From Uiverse.io by abrahamcalsin */ 
1115:1115:        .plan-card {
1116:1116:            background: #fff;
1117:1117:            width: 25rem;
1118:1118:            padding-left: 2.5rem;
1119:1119:            padding-right: 2.5rem;
1120:1120:            padding-top: 1.5rem;
1121:1121:            padding-bottom: 1.5rem;
1122:1122:            border-radius: 10px;
1123:1123:            border-bottom: 4px solid #000446;
1124:1124:            box-shadow: 0 6px 30px rgba(207, 212, 222, 0.3);
1125:1125:            font-family: "Poppins", sans-serif;
1126:1126:            position: relative;
1127:1127:        }
1128:1128:
1129:1129:        .plan-card h2 {
1130:1130:            margin-bottom: 1rem;
1131:1131:            font-size: 2rem;
1132:1132:            font-weight: 600;
1133:1133:        }
1134:1134:
1135:1135:        .plan-card h2 span {
1136:1136:            display: block;
1137:1137:            margin-top: 0.2rem;
1138:1138:            color: #4d4d4d;
1139:1139:            font-size: 1rem;
1140:1140:            font-weight: 400;
1141:1141:        }
1142:1142:
1143:1143:        .etiquet-price {
1144:1144:            position: relative;
1145:1145:            background: #fdbd4a;
1146:1146:            width: calc(100% + 5rem);
1147:1147:            margin-left: -2.5rem;
1148:1148:            padding: 0.5rem 2.5rem;
1149:1149:            border-radius: 5px 0 0 5px;
1150:1150:        }
1151:1151:
1152:1152:        .etiquet-price p {
1153:1153:            margin: 0;
1154:1154:            padding-top: 0.4rem;
1155:1155:            display: flex;
1156:1156:            font-size: 2.5rem;
1157:1157:            font-weight: 500;
1158:1158:        }
1159:1159:
1160:1160:        .etiquet-price p:before {
1161:1161:            content: "???";
1162:1162:            margin-right: 8px;
1163:1163:            font-size: 1.5rem;
1164:1164:            font-weight: 300;
1165:1165:            align-self: flex-start;
1166:1166:            margin-top: 0.2rem;
1167:1167:        }
1168:1168:
1169:1169:        .etiquet-price div {
1170:1170:            position: absolute;
1171:1171:            bottom: -23px;
1172:1172:            right: 0px;
1173:1173:            width: 0;
1174:1174:            height: 0;
1175:1175:            border-top: 13px solid #c58102;
1176:1176:            border-bottom: 10px solid transparent;
1177:1177:            border-right: 13px solid transparent;
1178:1178:            z-index: -6;
1179:1179:        }
1180:1180:
1181:1181:        .benefits-list {
1182:1182:            margin-top: 2rem;
1183:1183:        }
1184:1184:
1185:1185:        .benefits-list ul {
1186:1186:            padding: 0;
1187:1187:            font-size: 1rem;
1188:1188:        }
1189:1189:
1190:1190:        .benefits-list ul li {
1191:1191:            color: #4d4d4d;
1192:1192:            list-style: none;
1193:1193:            margin-bottom: 0.8rem;
1194:1194:            display: flex;
1195:1195:            align-items: center;
1196:1196:            gap: 1rem;
1197:1197:        }
1198:1198:
1199:1199:        .benefits-list ul li svg {
1200:1200:            width: 1.2rem;
1201:1201:            fill: #000446;
1202:1202:        }
1203:1203:
1204:1204:        .benefits-list ul li span {
1205:1205:            font-weight: 400;
1206:1206:        }
1207:1207:
1208:1208:        .button-get-plan {
1209:1209:            display: flex;
1210:1210:            justify-content: center;
1211:1211:            margin-top: 2rem;
1212:1212:        }
1213:1213:
1214:1214:        .button-get-plan a {
1215:1215:            display: flex;
1216:1216:            justify-content: center;
1217:1217:            align-items: center;
1218:1218:            background: #000446;
1219:1219:            color: #fff;
1220:1220:            padding: 1rem 2rem;
1221:1221:            border-radius: 8px;
1222:1222:            text-decoration: none;
1223:1223:            font-size: 1rem;
1224:1224:            letter-spacing: 0.05rem;
1225:1225:            font-weight: 500;
1226:1226:            transition: all 0.3s ease;
1227:1227:            width: 100%;
1228:1228:        }
1229:1229:
1230:1230:        .button-get-plan a:hover {
1231:1231:            transform: translateY(-3%);
1232:1232:            box-shadow: 0 3px 10px rgba(207, 212, 222, 0.9);
1233:1233:            background: #000660;
1234:1234:        }
1235:1235:
1236:1236:        .button-get-plan .svg-rocket {
1237:1237:            margin-right: 12px;
1238:1238:            width: 1.2rem;
1239:1239:            fill: currentColor;
1240:1240:        }
1241:1241:
1242:1242:        .close-modal {
1243:1243:            position: absolute;
1244:1244:            top: -15px;
1245:1245:            right: -15px;
1246:1246:            background: rgba(0, 0, 0, 0.5);
1247:1247:            border: none;
1248:1248:            color: white;
1249:1249:            font-size: 18px;
1250:1250:            cursor: pointer;
1251:1251:            padding: 8px;
1252:1252:            z-index: 2002;
1253:1253:            border-radius: 50%;
1254:1254:            width: 32px;
1255:1255:            height: 32px;
1256:1256:            display: flex;
1257:1257:            align-items: center;
1258:1258:            justify-content: center;
1259:1259:            transition: all 0.3s ease;
1260:1260:        }
1261:1261:
1262:1262:        .close-modal:hover {
1263:1263:            background: rgba(0, 0, 0, 0.8);
1264:1264:            transform: rotate(90deg);
1265:1265:        }
1266:1266:
1267:1267:        /* Table Styles */
1268:1268:        .card {
1269:1269:            background-color: #ffffff;
1270:1270:            width: 100%;
1271:1271:            max-width: 100%;
1272:1272:            max-height: 100%;
1273:1273:            display: flex;
1274:1274:            flex-direction: column;
1275:1275:            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
1276:1276:        }
1277:1277:
1278:1278:        .table-concept {
1279:1279:            width: 100%;
1280:1280:            height: 100%;
1281:1281:            max-height: 100%;
1282:1282:            overflow: auto;
1283:1283:            box-sizing: border-box;
1284:1284:        }
1285:1285:
1286:1286:        .table-concept .table-radio {
1287:1287:            display: none;
1288:1288:        }
1289:1289:
1290:1290:        .table-concept .table-radio:checked + .table-display {
1291:1291:            display: block;
1292:1292:        }
1293:1293:
1294:1294:        .table-concept .table-radio:checked + .table-display + table {
1295:1295:            width: 100%;
1296:1296:            display: table;
1297:1297:        }
1298:1298:
1299:1299:        .table-concept .table-radio:checked + .table-display + table + .pagination {
1300:1300:            display: flex;
1301:1301:        }
1302:1302:
1303:1303:        .table-concept .table-display {
1304:1304:            background-color: #e2e2e2;
1305:1305:            text-align: right;
1306:1306:            padding: 10px;
1307:1307:            display: none;
1308:1308:            position: sticky;
1309:1309:            left: 0;
1310:1310:        }
1311:1311:
1312:1312:        .table-concept table {
1313:1313:            background-color: #ffffff;
1314:1314:            font-size: 16px;
1315:1315:            border-collapse: collapse;
1316:1316:            display: none;
1317:1317:        }
1318:1318:
1319:1319:        .table-concept table tr:last-child td {
1320:1320:            border-bottom: 0;
1321:1321:        }
1322:1322:
1323:1323:        .table-concept table th,
1324:1324:        .table-concept table td {
1325:1325:            text-align: left;
1326:1326:            padding: 15px;
1327:1327:            box-sizing: border-box;
1328:1328:        }
1329:1329:
1330:1330:        .table-concept table th {
1331:1331:            color: #ffffff;
1332:1332:            font-weight: bold;
1333:1333:            background-color: rgb(3, 1, 43);
1334:1334:            border-bottom: solid 2px #d8d8d8;
1335:1335:            position: sticky;
1336:1336:            top: 0;
1337:1337:        }
1338:1338:
1339:1339:        .table-concept table td {
1340:1340:            border: solid 1px #d8d8d8;
1341:1341:            border-left: 0;
1342:1342:            border-right: 0;
1343:1343:            white-space: nowrap;
1344:1344:        }
1345:1345:
1346:1346:        .table-concept table tbody tr {
1347:1347:            transition: background-color 150ms ease-out;
1348:1348:        }
1349:1349:
1350:1350:        .table-concept table tbody tr:nth-child(2n) {
1351:1351:            background-color: #f5f5f5;
1352:1352:        }
1353:1353:
1354:1354:        .table-concept table tbody tr:hover {
1355:1355:            background-color: #ebebeb;
1356:1356:        }
1357:1357:
1358:1358:        .table-concept .pagination {
1359:1359:            background-color: #8f8f8f;
1360:1360:            width: 100%;
1361:1361:            display: none;
1362:1362:            position: sticky;
1363:1363:            bottom: 0;
1364:1364:            left: 0;
1365:1365:            padding: 0;
1366:1366:            margin: 0;
1367:1367:            justify-content: center;
1368:1368:            gap: 5px;
1369:1369:        }
1370:1370:
1371:1371:        .table-concept .pagination > label {
1372:1372:            color: #ffffff;
1373:1373:            padding: 8px 16px;
1374:1374:            cursor: pointer;
1375:1375:            background-color: #8f8f8f;
1376:1376:            border: none;
1377:1377:            transition: all 0.3s ease;
1378:1378:            user-select: none;
1379:1379:        }
1380:1380:
1381:1381:        .table-concept .pagination > label:not(.disabled):not(.active):hover {
1382:1382:            background-color: #767676;
1383:1383:        }
1384:1384:
1385:1385:        .table-concept .pagination > label.active {
1386:1386:            background-color: #2f2f2f;
1387:1387:            color: #ffffff;
1388:1388:            font-weight: bold;
1389:1389:        }
1390:1390:
1391:1391:        .table-concept .pagination > label.disabled {
1392:1392:            opacity: 0.6;
1393:1393:            cursor: not-allowed;
1394:1394:            background-color: #a8a8a8;
1395:1395:        }
1396:1396:
1397:1397:        /* Table Display Info */
1398:1398:        .table-display {
1399:1399:            background-color: #f8f9fa;
1400:1400:            color: #6c757d;
1401:1401:            font-size: 0.9rem;
1402:1402:            padding: 12px 20px;
1403:1403:            border-bottom: 1px solid #dee2e6;
1404:1404:        }
1405:1405:
1406:1406:        /* Hide all tables and pagination by default */
1407:1407:        .table-concept table,
1408:1408:        .table-concept .pagination {
1409:1409:            display: none;
1410:1410:        }
1411:1411:
1412:1412:        /* Show active table and its pagination */
1413:1413:        .table-concept .table-radio:checked + .table-display,
1414:1414:        .table-concept .table-radio:checked + .table-display + table,
1415:1415:        .table-concept .table-radio:checked + .table-display + table + .pagination {
1416:1416:            display: block;
1417:1417:        }
1418:1418:
1419:1419:        .table-concept .table-radio:checked + .table-display + table + .pagination {
1420:1420:            display: flex;
1421:1421:        }
1422:1422:
1423:1423:        /* Ensure table header stays on top */
1424:1424:        .table-concept table thead th {
1425:1425:            position: sticky;
1426:1426:            top: 0;
1427:1427:            z-index: 1;
1428:1428:            background-color: rgb(3, 1, 43);
1429:1429:            color: #ffffff;
1430:1430:            font-weight: bold;
1431:1431:        }
1432:1432:
1433:1433:        /* Additional style for all table headers */
1434:1434:        .table thead th {
1435:1435:            background-color: rgb(3, 1, 43) !important;
1436:1436:            color: #ffffff !important;
1437:1437:            font-weight: bold !important;
1438:1438:        }
1439:1439:
1440:1440:        /* Style for Bootstrap table headers */
1441:1441:        .table > :not(caption) > * > th {
1442:1442:            background-color: rgb(3, 1, 43) !important;
1443:1443:            color: #ffffff !important;
1444:1444:            font-weight: bold !important;
1445:1445:        }
1446:1446:
1447:1447:        .table-title {
1448:1448:            color: #ffffff;
1449:1449:            background-color: #2f2f2f;
1450:1450:            padding: 15px;
1451:1451:        }
1452:1452:
1453:1453:        .table-title h2 {
1454:1454:            margin: 0;
1455:1455:            padding: 0;
1456:1456:        }
1457:1457:
1458:1458:        .button-container {
1459:1459:            width: 100%;
1460:1460:            box-sizing: border-box;
1461:1461:            display: flex;
1462:1462:            justify-content: flex-end;
1463:1463:            padding: 10px;
1464:1464:            background-color: #fff;
1465:1465:            border-bottom: 1px solid #e5e7eb;
1466:1466:        }
1467:1467:
1468:1468:        .button-container span {
1469:1469:            color: #8f8f8f;
1470:1470:            text-align: right;
1471:1471:            min-height: 100%;
1472:1472:            display: flex;
1473:1473:            align-items: center;
1474:1474:            justify-content: center;
1475:1475:            margin-left: 10px;
1476:1476:            margin-right: 10px;
1477:1477:        }
1478:1478:
1479:1479:        .button-container button {
1480:1480:            font-family: inherit;
1481:1481:            font-size: inherit;
1482:1482:            color: #ffffff;
1483:1483:            padding: 10px 15px;
1484:1484:            border: 0;
1485:1485:            margin: 0;
1486:1486:            outline: 0;
1487:1487:            border-radius: 0;
1488:1488:            transition: background-color 225ms ease-out;
1489:1489:            cursor: pointer;
1490:1490:            display: flex;
1491:1491:            align-items: center;
1492:1492:            gap: 8px;
1493:1493:        }
1494:1494:
1495:1495:        .button-container button.primary {
1496:1496:            background-color: #147eff;
1497:1497:        }
1498:1498:
1499:1499:        .button-container button.primary:hover {
1500:1500:            background-color: #2e8fff;
1501:1501:        }
1502:1502:
1503:1503:        .button-container button.primary:active {
1504:1504:            background-color: #0066e6;
1505:1505:        }
1506:1506:
1507:1507:        .button-container button.danger {
1508:1508:            background-color: #d11800;
1509:1509:        }
1510:1510:
1511:1511:        .button-container button.danger:hover {
1512:1512:            background-color: #f01c00;
1513:1513:        }
1514:1514:
1515:1515:        .button-container button.danger:active {
1516:1516:            background-color: #b81600;
1517:1517:        }
1518:1518:
1519:1519:        .button-container button svg {
1520:1520:            fill: #ffffff;
1521:1521:            vertical-align: middle;
1522:1522:            padding: 0;
1523:1523:            margin: 0;
1524:1524:        }
1525:1525:
1526:1526:        /* Ensure table scrolls horizontally on mobile */
1527:1527:        @media (max-width: 768px) {
1528:1528:            .table-concept {
1529:1529:                overflow-x: auto;
1530:1530:            }
1531:1531:            
1532:1532:            .table-concept table {
1533:1533:                min-width: 800px;
1534:1534:            }
1535:1535:        }
1536:1536:
1537:1537:        /* Additional styles for premium badge */
1538:1538:        .premium-indicator {
1539:1539:            display: none;
1540:1540:        }
1541:1541:
1542:1542:        .premium-indicator::before {
1543:1543:            content: '';
1544:1544:            position: absolute;
1545:1545:            inset: 0;
1546:1546:            margin: auto;
1547:1547:            width: 50px;
1548:1548:            height: 50px;
1549:1549:            border-radius: inherit;
1550:1550:            scale: 0;
1551:1551:            z-index: -1;
1552:1552:            background-color: rgb(193, 163, 98);
1553:1553:            transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
1554:1554:        }
1555:1555:
1556:1556:        .premium-indicator:hover::before {
1557:1557:            scale: 3;
1558:1558:        }
1559:1559:
1560:1560:        .premium-indicator:hover {
1561:1561:            color: #212121;
1562:1562:            scale: 1.05;
1563:1563:            box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4);
1564:1564:        }
1565:1565:
1566:1566:        .premium-indicator:active {
1567:1567:            scale: 1;
1568:1568:        }
1569:1569:
1570:1570:        .premium-indicator i {
1571:1571:            font-size: 14px;
1572:1572:        }
1573:1573:
1574:1574:        /* Navbar premium indicator */
1575:1575:        .navbar-premium-indicator {
1576:1576:            display: none;
1577:1577:        }
1578:1578:
1579:1579:        .navbar-premium-indicator::before {
1580:1580:            content: '';
1581:1581:            position: absolute;
1582:1582:            inset: 0;
1583:1583:            margin: auto;
1584:1584:            width: 50px;
1585:1585:            height: 50px;
1586:1586:            border-radius: inherit;
1587:1587:            scale: 0;
1588:1588:            z-index: -1;
1589:1589:            background-color: rgb(193, 163, 98);
1590:1590:            transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
1591:1591:        }
1592:1592:
1593:1593:        .navbar-premium-indicator:hover::before {
1594:1594:            scale: 3;
1595:1595:        }
1596:1596:
1597:1597:        .navbar-premium-indicator:hover {
1598:1598:            color: #212121;
1599:1599:            scale: 1.05;
1600:1600:            box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4);
1601:1601:        }
1602:1602:
1603:1603:        .navbar-premium-indicator:active {
1604:1604:            scale: 1;
1605:1605:        }
1606:1606:
1607:1607:        .navbar-premium-indicator i {
1608:1608:            font-size: 12px;
1609:1609:        }
1610:1610:
1611:1611:        /* Navbar dark mode toggle styles */
1612:1612:        .navbar-dark-mode-toggle {
1613:1613:            margin-right: 15px;
1614:1614:            display: flex;
1615:1615:            align-items: center;
1616:1616:        }
1617:1617:
1618:1618:        .theme-switch {
1619:1619:            display: inline-block;
1620:1620:            position: relative;
1621:1621:            width: 50px;
1622:1622:            height: 24px;
1623:1623:            margin-bottom: 0;
1624:1624:        }
1625:1625:
1626:1626:        .theme-switch input {
1627:1627:            opacity: 0;
1628:1628:            width: 0;
1629:1629:            height: 0;
1630:1630:        }
1631:1631:
1632:1632:        .theme-slider {
1633:1633:            position: absolute;
1634:1634:            cursor: pointer;
1635:1635:            top: 0;
1636:1636:            left: 0;
1637:1637:            right: 0;
1638:1638:            bottom: 0;
1639:1639:            background-color: #ccc;
1640:1640:            transition: .4s;
1641:1641:            border-radius: 24px;
1642:1642:            display: flex;
1643:1643:            align-items: center;
1644:1644:            justify-content: space-between;
1645:1645:            padding: 0 5px;
1646:1646:        }
1647:1647:
1648:1648:        .theme-slider:before {
1649:1649:            position: absolute;
1650:1650:            content: "";
1651:1651:            height: 18px;
1652:1652:            width: 18px;
1653:1653:            left: 3px;
1654:1654:            bottom: 3px;
1655:1655:            background-color: white;
1656:1656:            transition: .4s;
1657:1657:            border-radius: 50%;
1658:1658:            z-index: 1;
1659:1659:        }
1660:1660:
1661:1661:        .theme-slider-icon {
1662:1662:            font-size: 12px;
1663:1663:            color: white;
1664:1664:            z-index: 0;
1665:1665:        }
1666:1666:
1667:1667:        .light-icon {
1668:1668:            margin-right: auto;
1669:1669:            color: #FFB300;
1670:1670:        }
1671:1671:
1672:1672:        .dark-icon {
1673:1673:            margin-left: auto;
1674:1674:            color: #5C6BC0;
1675:1675:        }
1676:1676:
1677:1677:        input:checked + .theme-slider {
1678:1678:            background-color: #375A7F;
1679:1679:        }
1680:1680:
1681:1681:        input:focus + .theme-slider {
1682:1682:            box-shadow: 0 0 1px #375A7F;
1683:1683:        }
1684:1684:
1685:1685:        input:checked + .theme-slider:before {
1686:1686:            transform: translateX(26px);
1687:1687:        }
1688:1688:
1689:1689:        /* More specific dark mode element styling */
1690:1690:        body.dark-mode .logo-container img {
1691:1691:            filter: var(--logo-filter);
1692:1692:        }
1693:1693:        
1694:1694:        body.dark-mode .tenant-name {
1695:1695:            color: var(--buk-text-color) !important;
1696:1696:            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
1697:1697:        }
1698:1698:        
1699:1699:        body.dark-mode .tenant-buk {
1700:1700:            color: var(--buk-only-color) !important;
1701:1701:        }
1702:1702:        
1703:1703:        body.dark-mode .user-avatar-container {
1704:1704:            border: 1px solid var(--avatar-border);
1705:1705:            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
1706:1706:        }
1707:1707:        
1708:1708:        body.dark-mode .badge {
1709:1709:            background-color: var(--badge-bg) !important;
1710:1710:            color: var(--badge-color) !important;
1711:1711:        }
1712:1712:        
1713:1713:        body.dark-mode .subscription-modal .subscription-content {
1714:1714:            background-color: var(--modal-bg);
1715:1715:        }
1716:1716:        
1717:1717:        body.dark-mode .plan-card {
1718:1718:            background-color: var(--card-bg);
1719:1719:            color: var(--text-color);
1720:1720:            border-color: var(--border-color);
1721:1721:        }
1722:1722:        
1723:1723:        body.dark-mode .dropdown-header {
1724:1724:            border-bottom-color: var(--border-color);
1725:1725:            color: var(--text-muted);
1726:1726:        }
1727:1727:        
1728:1728:        body.dark-mode .dropdown-divider {
1729:1729:            border-top-color: var(--border-color);
1730:1730:        }
1731:1731:        
1732:1732:        body.dark-mode .card-footer {
1733:1733:            background-color: rgba(255, 255, 255, 0.05);
1734:1734:            border-top-color: var(--border-color);
1735:1735:        }
1736:1736:        
1737:1737:        /* Sidebar dark mode styles */
1738:1738:        body.dark-mode .sidebar .nav-link {
1739:1739:            color: #e2e8f0;
1740:1740:        }
1741:1741:        
1742:1742:        body.dark-mode .sidebar .nav-link i {
1743:1743:            color: #a0aec0;
1744:1744:        }
1745:1745:        
1746:1746:        body.dark-mode .sidebar .nav-link:hover i,
1747:1747:        body.dark-mode .sidebar .nav-link.active i {
1748:1748:            color: #ffffff;
1749:1749:        }
1750:1750:        
1751:1751:        body.dark-mode .sidebar .dropdown-item {
1752:1752:            color: #e2e8f0;
1753:1753:        }
1754:1754:        
1755:1755:        body.dark-mode .sidebar .dropdown-item i {
1756:1756:            color: #a0aec0;
1757:1757:        }
1758:1758:        
1759:1759:        body.dark-mode .sidebar .dropdown-item:hover i {
1760:1760:            color: #ffffff;
1761:1761:        }
1762:1762:        
1763:1763:        body.dark-mode .sidebar .tenant-name {
1764:1764:            color: #ffffff !important;
1765:1765:        }
1766:1766:        
1767:1767:        body.dark-mode .sidebar .dropdown-menu {
1768:1768:            background-color: #1a202c;
1769:1769:            border-color: #2d3748;
1770:1770:        }
1771:1771:        
1772:1772:        body.dark-mode .sidebar .nav-link.dropdown-toggle .dropdown-icon {
1773:1773:            color: #a0aec0;
1774:1774:        }
1775:1775:        
1776:1776:        body.dark-mode .sidebar .nav-link.dropdown-toggle:hover .dropdown-icon,
1777:1777:        body.dark-mode .sidebar .nav-link.dropdown-toggle.show .dropdown-icon {
1778:1778:            color: #ffffff;
1779:1779:        }
1780:1780:        
1781:1781:        /* Logo container styles */
1782:1782:        .dark-mode .logo-container {
1783:1783:            filter: var(--logo-filter, brightness(1.2) contrast(1.2));
1784:1784:        }
1785:1785:        .dark-mode .tenant-name {
1786:1786:            color: var(--accent-color, #f0f0f0);
1787:1787:        }
1788:1788:        .dark-mode .user-avatar {
1789:1789:            border: var(--avatar-border, 2px solid #444);
1790:1790:            filter: brightness(0.9);
1791:1791:        }
1792:1792:        .dark-mode .badge {
1793:1793:            background-color: var(--badge-bg, #2c2c2c);
1794:1794:            color: var(--accent-color, #f0f0f0);
1795:1795:        }
1796:1796:        .dark-mode .subscription-modal,
1797:1797:        .dark-mode .plan-card {
1798:1798:            background-color: var(--modal-bg, #1e1e1e);
1799:1799:            border: 1px solid var(--border-color, #444);
1800:1800:            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
1801:1801:        }
1802:1802:        .dark-mode .dropdown-header,
1803:1803:        .dark-mode .dropdown-divider {
1804:1804:            border-color: var(--border-color, #444);
1805:1805:        }
1806:1806:        .dark-mode .card-footer {
1807:1807:            background-color: var(--card-bg, #2c2c2c);
1808:1808:            border-color: var(--border-color, #444);
1809:1809:        }
1810:1810:
1811:1811:        /* Navigation Dark Mode Styles */
1812:1812:        .dark-mode .side-navbar {
1813:1813:            background-color: var(--nav-bg, #1a1a1a);
1814:1814:            border-right: 1px solid var(--border-color, #444);
1815:1815:            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
1816:1816:        }
1817:1817:
1818:1818:        .dark-mode .side-navbar li a,
1819:1819:        .dark-mode .side-navbar .dropdown-menu {
1820:1820:            color: var(--text-color, #e0e0e0);
1821:1821:            background-color: var(--nav-bg, #1a1a1a);
1822:1822:        }
1823:1823:
1824:1824:        .dark-mode .side-navbar li a:hover {
1825:1825:            background-color: var(--hover-bg, #2c2c2c);
1826:1826:        }
1827:1827:
1828:1828:        .dark-mode .side-navbar li.active > a {
1829:1829:            background-color: var(--active-bg, #333);
1830:1830:            color: var(--active-text, #ffffff);
1831:1831:            border-left: 3px solid var(--active-border, #d4af37);
1832:1832:        }
1833:1833:
1834:1834:        .dark-mode .side-navbar .dropdown-menu {
1835:1835:            border: 1px solid var(--border-color, #444);
1836:1836:            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
1837:1837:        }
1838:1838:
1839:1839:        .dark-mode .side-navbar .dropdown-item {
1840:1840:            color: var(--text-color, #e0e0e0);
1841:1841:        }
1842:1842:
1843:1843:        .dark-mode .side-navbar .dropdown-item:hover {
1844:1844:            background-color: var(--hover-bg, #2c2c2c);
1845:1845:            color: var(--hover-text, #ffffff);
1846:1846:        }
1847:1847:
1848:1848:        .dark-mode .side-navbar .dropdown-toggle::after {
1849:1849:            color: var(--accent-color, #d4af37);
1850:1850:        }
1851:1851:
1852:1852:        .dark-mode .nav-icon {
1853:1853:            color: var(--icon-color, #d4af37);
1854:1854:            filter: brightness(1.2);
1855:1855:        }
1856:1856:
1857:1857:        .dark-mode .menu-button {
1858:1858:            background-color: var(--button-bg, #2c2c2c);
1859:1859:            color: var(--text-color, #e0e0e0);
1860:1860:            border-color: var(--border-color, #444);
1861:1861:        }
1862:1862:
1863:1863:        .dark-mode .menu-button:hover {
1864:1864:            background-color: var(--button-hover-bg, #333);
1865:1865:        }
1866:1866:
1867:1867:        /* Tables, Content and Form Elements Dark Mode */
1868:1868:        .dark-mode .table {
1869:1869:            color: var(--text-color, #e0e0e0);
1870:1870:            border-color: var(--border-color, #444);
1871:1871:        }
1872:1872:
1873:1873:        .dark-mode .table th,
1874:1874:        .dark-mode .table td {
1875:1875:            border-color: var(--border-color, #444);
1876:1876:        }
1877:1877:
1878:1878:        .dark-mode .table thead th {
1879:1879:            background-color: var(--table-header-bg, #2c2c2c);
1880:1880:            color: var(--text-color, #e0e0e0);
1881:1881:            border-bottom: 2px solid var(--border-color, #444);
1882:1882:        }
1883:1883:
1884:1884:        .dark-mode .table-striped tbody tr:nth-of-type(odd) {
1885:1885:            background-color: var(--table-stripe-bg, #252525);
1886:1886:        }
1887:1887:
1888:1888:        .dark-mode .table-hover tbody tr:hover {
1889:1889:            background-color: var(--table-hover-bg, #333);
1890:1890:        }
1891:1891:
1892:1892:        .dark-mode .content-section,
1893:1893:        .dark-mode .card,
1894:1894:        .dark-mode .card-header,
1895:1895:        .dark-mode .card-body {
1896:1896:            background-color: var(--card-bg, #1e1e1e);
1897:1897:            color: var(--text-color, #e0e0e0);
1898:1898:            border-color: var(--border-color, #444);
1899:1899:        }
1900:1900:
1901:1901:        .dark-mode .card-header {
1902:1902:            background-color: var(--card-header-bg, #2c2c2c);
1903:1903:            border-bottom: 1px solid var(--border-color, #444);
1904:1904:        }
1905:1905:
1906:1906:        .dark-mode .form-control,
1907:1907:        .dark-mode .form-select,
1908:1908:        .dark-mode .input-group-text {
1909:1909:            background-color: var(--input-bg, #2c2c2c);
1910:1910:            color: var(--text-color, #e0e0e0);
1911:1911:            border-color: var(--border-color, #444);
1912:1912:        }
1913:1913:
1914:1914:        .dark-mode .form-control:focus,
1915:1915:        .dark-mode .form-select:focus {
1916:1916:            background-color: var(--input-focus-bg, #333);
1917:1917:            color: var(--text-color, #e0e0e0);
1918:1918:            border-color: var(--input-focus-border, #d4af37);
1919:1919:            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
1920:1920:        }
1921:1921:
1922:1922:        .dark-mode .form-control::placeholder {
1923:1923:            color: var(--placeholder-color, #888);
1924:1924:        }
1925:1925:
1926:1926:        .dark-mode .btn-primary {
1927:1927:            background-color: var(--primary-btn-bg, #3a3a3a);
1928:1928:            border-color: var(--primary-btn-border, #444);
1929:1929:            color: var(--primary-btn-text, #e0e0e0);
1930:1930:        }
1931:1931:
1932:1932:        .dark-mode .btn-primary:hover,
1933:1933:        .dark-mode .btn-primary:focus {
1934:1934:            background-color: var(--primary-btn-hover-bg, #444);
1935:1935:            border-color: var(--primary-btn-hover-border, #555);
1936:1936:            color: var(--primary-btn-hover-text, #ffffff);
1937:1937:        }
1938:1938:
1939:1939:        .dark-mode .btn-secondary {
1940:1940:            background-color: var(--secondary-btn-bg, #2c2c2c);
1941:1941:            border-color: var(--secondary-btn-border, #444);
1942:1942:            color: var(--secondary-btn-text, #e0e0e0);
1943:1943:        }
1944:1944:
1945:1945:        .dark-mode .btn-secondary:hover,
1946:1946:        .dark-mode .btn-secondary:focus {
1947:1947:            background-color: var(--secondary-btn-hover-bg, #3a3a3a);
1948:1948:            border-color: var(--secondary-btn-hover-border, #555);
1949:1949:        }
1950:1950:
1951:1951:        .dark-mode .pagination .page-item .page-link {
1952:1952:            background-color: var(--pagination-bg, #2c2c2c);
1953:1953:            color: var(--text-color, #e0e0e0);
1954:1954:            border-color: var(--border-color, #444);
1955:1955:        }
1956:1956:
1957:1957:        .dark-mode .pagination .page-item.active .page-link {
1958:1958:            background-color: var(--pagination-active-bg, #d4af37);
1959:1959:            color: var(--pagination-active-text, #333);
1960:1960:            border-color: var(--pagination-active-border, #d4af37);
1961:1961:        }
1962:1962:
1963:1963:        .dark-mode .pagination .page-item .page-link:hover {
1964:1964:            background-color: var(--pagination-hover-bg, #3a3a3a);
1965:1965:            color: var(--text-color, #e0e0e0);
1966:1966:            border-color: var(--border-color, #555);
1967:1967:        }
1968:1968:
1969:1969:        /* Alerts and Notifications Dark Mode */
1970:1970:        .dark-mode .alert {
1971:1971:            background-color: var(--alert-bg, #2c2c2c);
1972:1972:            color: var(--text-color, #e0e0e0);
1973:1973:            border-color: var(--border-color, #444);
1974:1974:        }
1975:1975:
1976:1976:        .dark-mode .alert-success {
1977:1977:            background-color: rgba(40, 167, 69, 0.2);
1978:1978:            color: #98c379;
1979:1979:            border-color: rgba(40, 167, 69, 0.3);
1980:1980:        }
1981:1981:
1982:1982:        .dark-mode .alert-info {
1983:1983:            background-color: rgba(23, 162, 184, 0.2);
1984:1984:            color: #61afef;
1985:1985:            border-color: rgba(23, 162, 184, 0.3);
1986:1986:        }
1987:1987:
1988:1988:        .dark-mode .alert-warning {
1989:1989:            background-color: rgba(255, 193, 7, 0.2);
1990:1990:            color: #e5c07b;
1991:1991:            border-color: rgba(255, 193, 7, 0.3);
1992:1992:        }
1993:1993:
1994:1994:        .dark-mode .alert-danger {
1995:1995:            background-color: rgba(220, 53, 69, 0.2);
1996:1996:            color: #e06c75;
1997:1997:            border-color: rgba(220, 53, 69, 0.3);
1998:1998:        }
1999:1999:
2000:2000:        .dark-mode .toast {
2001:2001:            background-color: var(--modal-bg);
2002:2002:            color: var(--text-color);
2003:2003:            border: 1px solid var(--border-color);
2004:2004:            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
2005:2005:        }
2006:2006:
2007:2007:        .dark-mode .toast-header {
2008:2008:            background-color: var(--card-header-bg);
2009:2009:            color: var(--text-color);
2010:2010:            border-bottom: 1px solid var(--border-color);
2011:2011:        }
2012:2012:
2013:2013:        .dark-mode .toast-body {
2014:2014:            background-color: var(--modal-bg);
2015:2015:            color: var(--text-color);
2016:2016:        }
2017:2017:
2018:2018:        .dark-mode .toast .close {
2019:2019:            color: var(--text-color);
2020:2020:            text-shadow: none;
2021:2021:        }
2022:2022:        
2023:2023:        .dark-mode .toast .btn-close-white {
2024:2024:            opacity: 0.8;
2025:2025:        }
2026:2026:        
2027:2027:        .dark-mode .toast .btn-close-white:hover {
2028:2028:            opacity: 1;
2029:2029:        }
2030:2030:        
2031:2031:        .dark-mode .toast-container {
2032:2032:            z-index: 1060;
2033:2033:        }
2034:2034:        
2035:2035:        .dark-mode #themeToast {
2036:2036:            background-color: var(--accent-color);
2037:2037:        }
2038:2038:        
2039:2039:        .dark-mode #themeToast .toast-body {
2040:2040:            background-color: transparent;
2041:2041:            color: #212529;
2042:2042:        }
2043:2043:
2044:2044:        /* Settings modal styles for dark mode */
2045:2045:        .dark-mode .settings-modal .modal-content {
2046:2046:            background-color: var(--modal-bg);
2047:2047:            color: var(--text-color);
2048:2048:            border: 1px solid var(--border-color);
2049:2049:        }
2050:2050:
2051:2051:        .dark-mode .settings-modal .modal-header {
2052:2052:            border-bottom: 1px solid var(--border-color);
2053:2053:        }
2054:2054:
2055:2055:        .dark-mode .settings-modal .modal-footer {
2056:2056:            border-top: 1px solid var(--border-color);
2057:2057:        }
2058:2058:
2059:2059:        .dark-mode .settings-list .settings-item {
2060:2060:            border-bottom: 1px solid var(--border-color);
2061:2061:        }
2062:2062:
2063:2063:        .dark-mode .settings-list .settings-item:last-child {
2064:2064:            border-bottom: none;
2065:2065:        }
2066:2066:
2067:2067:        .dark-mode .settings-list .settings-label {
2068:2068:            color: var(--text-color);
2069:2069:        }
2070:2070:
2071:2071:        .dark-mode .settings-list .settings-description {
2072:2072:            color: var(--text-secondary);
2073:2073:        }
2074:2074:
2075:2075:        /* Switch toggle for dark mode */
2076:2076:        .dark-mode .switch-toggle {
2077:2077:            background-color: var(--border-color);
2078:2078:        }
2079:2079:
2080:2080:        .dark-mode .switch-toggle.checked {
2081:2081:            background-color: var(--accent-color);
2082:2082:        }
2083:2083:
2084:2084:        .dark-mode .switch-toggle .toggle-handle {
2085:2085:            background-color: white;
2086:2086:        }
2087:2087:
2088:2088:        .dark-mode .tooltip .tooltip-inner {
2089:2089:            background-color: var(--tooltip-bg, #333);
2090:2090:            color: var(--tooltip-text, #e0e0e0);
2091:2091:        }
2092:2092:
2093:2093:        .dark-mode .tooltip .arrow::before {
2094:2094:            border-top-color: var(--tooltip-bg, #333);
2095:2095:        }
2096:2096:
2097:2097:        .dark-mode .popover {
2098:2098:            background-color: var(--popover-bg, #252525);
2099:2099:            border-color: var(--border-color, #444);
2100:2100:        }
2101:2101:
2102:2102:        .dark-mode .popover-header {
2103:2103:            background-color: var(--popover-header-bg, #2c2c2c);
2104:2104:            color: var(--text-color, #e0e0e0);
2105:2105:            border-bottom-color: var(--border-color, #444);
2106:2106:        }
2107:2107:
2108:2108:        .dark-mode .popover-body {
2109:2109:            color: var(--text-color, #e0e0e0);
2110:2110:        }
2111:2111:
2112:2112:        .dark-mode .modal-content {
2113:2113:            background-color: var(--buk-dark-bg-secondary);
2114:2114:            color: var(--buk-light-text-color);
2115:2115:            border: 1px solid var(--buk-dark-border-color);
2116:2116:        }
2117:2117:
2118:2118:        .dark-mode .modal-header {
2119:2119:            border-bottom: 1px solid var(--buk-dark-border-color);
2120:2120:        }
2121:2121:
2122:2122:        .dark-mode .modal-footer {
2123:2123:            border-top: 1px solid var(--buk-dark-border-color);
2124:2124:        }
2125:2125:
2126:2126:        .dark-mode .close {
2127:2127:            color: var(--buk-light-text-color);
2128:2128:        }
2129:2129:
2130:2130:        .dark-mode .btn-close {
2131:2131:            filter: invert(1) grayscale(100%) brightness(200%);
2132:2132:        }
2133:2133:
2134:2134:        /* Progress bars and loaders */
2135:2135:        .dark-mode .progress {
2136:2136:            background-color: var(--progress-bg, #2c2c2c);
2137:2137:        }
2138:2138:
2139:2139:        .dark-mode .progress-bar {
2140:2140:            background-color: var(--progress-bar-bg, #d4af37);
2141:2141:        }
2142:2142:
2143:2143:        /* Lists dark mode styles */
2144:2144:        .dark-mode .list-group {
2145:2145:            background-color: transparent;
2146:2146:        }
2147:2147:
2148:2148:        .dark-mode .list-group-item {
2149:2149:            background-color: var(--list-item-bg, #252525);
2150:2150:            color: var(--text-color, #e0e0e0);
2151:2151:            border-color: var(--border-color, #444);
2152:2152:        }
2153:2153:
2154:2154:        .dark-mode .list-group-item-action:hover,
2155:2155:        .dark-mode .list-group-item-action:focus {
2156:2156:            background-color: var(--list-item-hover-bg, #333);
2157:2157:            color: var(--text-color, #e0e0e0);
2158:2158:        }
2159:2159:
2160:2160:        .dark-mode .list-group-item.active {
2161:2161:            background-color: var(--accent-color, #d4af37);
2162:2162:            color: #212529;
2163:2163:            border-color: var(--accent-color, #d4af37);
2164:2164:        }
2165:2165:
2166:2166:        /* Tabs and pills dark mode styles */
2167:2167:        .dark-mode .nav-tabs,
2168:2168:        .dark-mode .nav-pills {
2169:2169:            border-color: var(--border-color, #444);
2170:2170:        }
2171:2171:
2172:2172:        .dark-mode .nav-tabs .nav-link,
2173:2173:        .dark-mode .nav-pills .nav-link {
2174:2174:            color: var(--text-color, #e0e0e0);
2175:2175:        }
2176:2176:
2177:2177:        .dark-mode .nav-tabs .nav-link:hover,
2178:2178:        .dark-mode .nav-pills .nav-link:hover {
2179:2179:            border-color: var(--border-color, #444);
2180:2180:            background-color: var(--tab-hover-bg, #2c2c2c);
2181:2181:        }
2182:2182:
2183:2183:        .dark-mode .nav-tabs .nav-link.active,
2184:2184:        .dark-mode .nav-pills .nav-link.active {
2185:2185:            background-color: var(--tab-active-bg, #d4af37);
2186:2186:            color: #212529;
2187:2187:            border-color: var(--border-color, #444);
2188:2188:        }
2189:2189:
2190:2190:        .dark-mode .tab-content {
2191:2191:            background-color: var(--tab-content-bg, #252525);
2192:2192:            border-color: var(--border-color, #444);
2193:2193:        }
2194:2194:
2195:2195:        /* Breadcrumbs dark mode */
2196:2196:        .dark-mode .breadcrumb {
2197:2197:            background-color: var(--breadcrumb-bg, #2c2c2c);
2198:2198:        }
2199:2199:
2200:2200:        .dark-mode .breadcrumb-item {
2201:2201:            color: var(--text-color, #e0e0e0);
2202:2202:        }
2203:2203:
2204:2204:        .dark-mode .breadcrumb-item.active {
2205:2205:            color: var(--breadcrumb-active, #d4af37);
2206:2206:        }
2207:2207:
2208:2208:        .dark-mode .breadcrumb-item + .breadcrumb-item::before {
2209:2209:            color: var(--breadcrumb-divider, #6c757d);
2210:2210:        }
2211:2211:
2212:2212:        /* Miscellaneous elements */
2213:2213:        .dark-mode code,
2214:2214:        .dark-mode pre {
2215:2215:            background-color: var(--code-bg, #2d2d2d);
2216:2216:            color: var(--code-color, #e6e6e6);
2217:2217:            border-color: var(--border-color, #444);
2218:2218:        }
2219:2219:
2220:2220:        .dark-mode hr {
2221:2221:            border-top-color: var(--border-color, #444);
2222:2222:        }
2223:2223:
2224:2224:        .dark-mode .dropdown-item.active,
2225:2225:        .dark-mode .dropdown-item:active {
2226:2226:            background-color: var(--accent-color, #d4af37);
2227:2227:            color: #212529;
2228:2228:        }
2229:2229:
2230:2230:        /* Premium elements in dark mode */
2231:2231:        .dark-mode .premium-button,
2232:2232:        .premium-button {
2233:2233:          cursor: pointer;
2234:2234:          position: relative;
2235:2235:          padding: 6px 16px;
2236:2236:          font-size: 14px;
2237:2237:          color: rgb(193, 163, 98) !important;
2238:2238:          border: 2px solid rgb(193, 163, 98) !important;
2239:2239:          border-radius: 25px;
2240:2240:          background-color: transparent !important;
2241:2241:          font-weight: 600;
2242:2242:          transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
2243:2243:          overflow: hidden;
2244:2244:          margin: 0.3rem;
2245:2245:          display: inline-flex;
2246:2246:          align-items: center;
2247:2247:          justify-content: center;
2248:2248:          text-decoration: none;
2249:2249:        }
2250:2250:
2251:2251:        .dark-mode .premium-button::before,
2252:2252:        .premium-button::before {
2253:2253:          content: '';
2254:2254:          position: absolute;
2255:2255:          inset: 0;
2256:2256:          margin: auto;
2257:2257:          width: 40px;
2258:2258:          height: 40px;
2259:2259:          border-radius: inherit;
2260:2260:          scale: 0;
2261:2261:          z-index: -1;
2262:2262:          background-color: rgb(193, 163, 98) !important;
2263:2263:          transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
2264:2264:        }
2265:2265:
2266:2266:        .dark-mode .premium-button:hover::before,
2267:2267:        .premium-button:hover::before {
2268:2268:          scale: 3;
2269:2269:        }
2270:2270:
2271:2271:        .dark-mode .premium-button:hover,
2272:2272:        .premium-button:hover {
2273:2273:          color: #212121 !important;
2274:2274:          scale: 1.1;
2275:2275:          box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4) !important;
2276:2276:          background-color: transparent !important;
2277:2277:          text-decoration: none;
2278:2278:        }
2279:2279:
2280:2280:        .dark-mode .premium-button:active,
2281:2281:        .premium-button:active {
2282:2282:          scale: 1;
2283:2283:        }
2284:2284:
2285:2285:        .dark-mode .premium-button i,
2286:2286:        .premium-button i {
2287:2287:          margin-right: 8px;
2288:2288:          color: rgb(193, 163, 98) !important;
2289:2289:          transition: all 0.3s ease;
2290:2290:        }
2291:2291:
2292:2292:        .dark-mode .premium-button:hover i,
2293:2293:        .premium-button:hover i {
2294:2294:          color: #212121 !important;
2295:2295:        }
2296:2296:
2297:2297:        /* Premium badge styles */
2298:2298:        .dark-mode .premium-badge,
2299:2299:        .premium-badge {
2300:2300:          display: inline-flex;
2301:2301:          align-items: center;
2302:2302:          color: rgb(193, 163, 98) !important;
2303:2303:          border: 1px solid rgb(193, 163, 98) !important;
2304:2304:          border-radius: 25px;
2305:2305:          background-color: transparent !important;
2306:2306:          padding: 4px 10px;
2307:2307:          font-weight: 600;
2308:2308:          position: relative;
2309:2309:          overflow: hidden;
2310:2310:          transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
2311:2311:          font-size: 0.75rem;
2312:2312:        }
2313:2313:
2314:2314:        .dark-mode .premium-badge::before,
2315:2315:        .premium-badge::before {
2316:2316:          content: '';
2317:2317:          position: absolute;
2318:2318:          inset: 0;
2319:2319:          margin: auto;
2320:2320:          width: 30px;
2321:2321:          height: 30px;
2322:2322:          border-radius: inherit;
2323:2323:          scale: 0;
2324:2324:          z-index: -1;
2325:2325:          background-color: rgb(193, 163, 98) !important;
2326:2326:          transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
2327:2327:        }
2328:2328:
2329:2329:        .dark-mode .premium-badge:hover::before,
2330:2330:        .premium-badge:hover::before {
2331:2331:          scale: 3;
2332:2332:        }
2333:2333:
2334:2334:        .dark-mode .premium-badge:hover,
2335:2335:        .premium-badge:hover {
2336:2336:          color: #212121 !important;
2337:2337:          scale: 1.1;
2338:2338:          box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4) !important;
2339:2339:        }
2340:2340:
2341:2341:        .dark-mode .premium-badge:active,
2342:2342:        .premium-badge:active {
2343:2343:          scale: 1;
2344:2344:        }
2345:2345:
2346:2346:        .dark-mode .premium-badge i,
2347:2347:        .premium-badge i {
2348:2348:          margin-right: 0.4rem;
2349:2349:          font-size: 0.875rem;
2350:2350:          position: relative;
2351:2351:          z-index: 1;
2352:2352:          color: rgb(193, 163, 98) !important;
2353:2353:        }
2354:2354:
2355:2355:        .dark-mode .premium-badge span,
2356:2356:        .premium-badge span {
2357:2357:          position: relative;
2358:2358:          z-index: 1;
2359:2359:          color: rgb(193, 163, 98) !important;
2360:2360:        }
2361:2361:
2362:2362:        .dark-mode .premium-badge:hover i,
2363:2363:        .premium-badge:hover i,
2364:2364:        .dark-mode .premium-badge:hover span,
2365:2365:        .premium-badge:hover span {
2366:2366:          color: #212121 !important;
2367:2367:        }
2368:2368:
2369:2369:        /* Button Get Plan and Etiquet Price updated styles */
2370:2370:        .dark-mode .button-get-plan a,
2371:2371:        .button-get-plan a {
2372:2372:          cursor: pointer;
2373:2373:          position: relative;
2374:2374:          padding: 10px 24px;
2375:2375:          font-size: 16px;
2376:2376:          color: rgb(193, 163, 98) !important;
2377:2377:          border: 2px solid rgb(193, 163, 98) !important;
2378:2378:          border-radius: 34px;
2379:2379:          background-color: transparent !important;
2380:2380:          font-weight: 600;
2381:2381:          transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
2382:2382:          overflow: hidden;
2383:2383:          display: inline-flex;
2384:2384:          align-items: center;
2385:2385:          justify-content: center;
2386:2386:          text-decoration: none;
2387:2387:        }
2388:2388:
2389:2389:        .dark-mode .button-get-plan a::before,
2390:2390:        .button-get-plan a::before {
2391:2391:          content: '';
2392:2392:          position: absolute;
2393:2393:          inset: 0;
2394:2394:          margin: auto;
2395:2395:          width: 50px;
2396:2396:          height: 50px;
2397:2397:          border-radius: inherit;
2398:2398:          scale: 0;
2399:2399:          z-index: -1;
2400:2400:          background-color: rgb(193, 163, 98) !important;
2401:2401:          transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
2402:2402:        }
2403:2403:
2404:2404:        .dark-mode .button-get-plan a:hover::before,
2405:2405:        .button-get-plan a:hover::before {
2406:2406:          scale: 3;
2407:2407:        }
2408:2408:
2409:2409:        .dark-mode .button-get-plan a:hover,
2410:2410:        .button-get-plan a:hover {
2411:2411:          color: #212121 !important;
2412:2412:          scale: 1.1;
2413:2413:          box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4) !important;
2414:2414:          text-decoration: none;
2415:2415:        }
2416:2416:
2417:2417:        .dark-mode .button-get-plan .svg-rocket,
2418:2418:        .button-get-plan .svg-rocket {
2419:2419:          margin-right: 10px;
2420:2420:          width: .9rem;
2421:2421:          fill: rgb(193, 163, 98) !important;
2422:2422:          transition: all 0.3s ease;
2423:2423:        }
2424:2424:
2425:2425:        .dark-mode .button-get-plan a:hover .svg-rocket,
2426:2426:        .button-get-plan a:hover .svg-rocket {
2427:2427:          fill: #212121 !important;
2428:2428:        }
2429:2429:
2430:2430:        .dark-mode .etiquet-price,
2431:2431:        .etiquet-price {
2432:2432:          position: relative;
2433:2433:          background-color: transparent !important;
2434:2434:          border: 2px solid rgb(193, 163, 98) !important;
2435:2435:          color: rgb(193, 163, 98) !important;
2436:2436:          width: 14.46rem;
2437:2437:          margin-left: -0.65rem;
2438:2438:          padding: .2rem 1.2rem;
2439:2439:          border-radius: 5px 0 0 5px;
2440:2440:          transition: all 0.3s ease;
2441:2441:        }
2442:2442:
2443:2443:        .dark-mode .etiquet-price p,
2444:2444:        .dark-mode .etiquet-price p:before,
2445:2445:        .dark-mode .etiquet-price p:after,
2446:2446:        .etiquet-price p,
2447:2447:        .etiquet-price p:before,
2448:2448:        .etiquet-price p:after {
2449:2449:          color: rgb(193, 163, 98) !important;
2450:2450:        }
2451:2451:
2452:2452:        /* Dark mode styles for logo, avatar, badges, and modals */
2453:2453:        .dark-mode .logo-container img {
2454:2454:            filter: var(--logo-filter);
2455:2455:        }
2456:2456:        
2457:2457:        .dark-mode .tenant-name {
2458:2458:            color: var(--buk-text-color);
2459:2459:        }
2460:2460:        
2461:2461:        .dark-mode .user-avatar {
2462:2462:            border: 2px solid var(--avatar-border);
2463:2463:        }
2464:2464:        
2465:2465:        .dark-mode .badge {
2466:2466:            background-color: var(--badge-bg);
2467:2467:            color: var(--badge-color);
2468:2468:        }
2469:2469:        
2470:2470:        .dark-mode .subscription-modal .modal-content {
2471:2471:            background-color: var(--modal-bg);
2472:2472:            color: var(--text-color);
2473:2473:            border-color: var(--border-color);
2474:2474:        }
2475:2475:        
2476:2476:        .dark-mode .plan-card {
2477:2477:            background-color: var(--card-bg);
2478:2478:            border-color: var(--border-color);
2479:2479:        }
2480:2480:        
2481:2481:        .dark-mode .dropdown-header {
2482:2482:            color: var(--text-muted);
2483:2483:            border-bottom-color: var(--border-color);
2484:2484:        }
2485:2485:        
2486:2486:        .dark-mode .dropdown-divider {
2487:2487:            border-color: var(--border-color);
2488:2488:        }
2489:2489:        
2490:2490:        .dark-mode .card-footer {
2491:2491:            background-color: rgba(31, 41, 55, 0.5);
2492:2492:            border-top-color: var(--border-color);
2493:2493:        }
2494:2494:        
2495:2495:        /* Dark mode styles for tables, content sections, buttons, and form elements */
2496:2496:        .dark-mode .table {
2497:2497:            color: var(--text-color);
2498:2498:        }
2499:2499:        
2500:2500:        .dark-mode .table thead th {
2501:2501:            background-color: rgba(31, 41, 55, 0.7);
2502:2502:            color: var(--text-color);
2503:2503:            border-color: var(--border-color);
2504:2504:        }
2505:2505:        
2506:2506:        .dark-mode .table td, 
2507:2507:        .dark-mode .table th {
2508:2508:            border-color: var(--border-color);
2509:2509:        }
2510:2510:        
2511:2511:        .dark-mode .content-section {
2512:2512:            background-color: var(--card-bg);
2513:2513:            border-color: var(--border-color);
2514:2514:        }
2515:2515:        
2516:2516:        .dark-mode .btn-primary {
2517:2517:            background-color: var(--primary-color);
2518:2518:            border-color: var(--primary-color);
2519:2519:        }
2520:2520:        
2521:2521:        .dark-mode .btn-primary:hover {
2522:2522:            background-color: var(--primary-hover);
2523:2523:            border-color: var(--primary-hover);
2524:2524:        }
2525:2525:        
2526:2526:        .dark-mode .btn-secondary {
2527:2527:            background-color: #4B5563;
2528:2528:            border-color: #4B5563;
2529:2529:            color: #ffffff;
2530:2530:        }
2531:2531:        
2532:2532:        .dark-mode .btn-secondary:hover {
2533:2533:            background-color: #374151;
2534:2534:            border-color: #374151;
2535:2535:        }
2536:2536:        
2537:2537:        .dark-mode .form-control,
2538:2538:        .dark-mode .form-select {
2539:2539:            background-color: var(--input-bg);
2540:2540:            border-color: var(--input-border);
2541:2541:            color: var(--text-color);
2542:2542:        }
2543:2543:        
2544:2544:        .dark-mode .form-control:focus,
2545:2545:        .dark-mode .form-select:focus {
2546:2546:            border-color: var(--primary-color);
2547:2547:            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
2548:2548:        }
2549:2549:        
2550:2550:        .dark-mode .pagination .page-link {
2551:2551:            background-color: var(--card-bg);
2552:2552:            border-color: var(--border-color);
2553:2553:            color: var(--text-color);
2554:2554:        }
2555:2555:        
2556:2556:        .dark-mode .pagination .page-link:hover {
2557:2557:            background-color: var(--hover-bg);
2558:2558:            border-color: var(--border-color);
2559:2559:        }
2560:2560:        
2561:2561:        .dark-mode .pagination .page-item.active .page-link {
2562:2562:            background-color: var(--primary-color);
2563:2563:            border-color: var(--primary-color);
2564:2564:            color: #ffffff;
2565:2565:        }
2566:2566:        
2567:2567:        /* Card examples - preserved light appearance in dark mode */
2568:2568:        .dark-mode .card-examples-wrapper {
2569:2569:            background-color: #ffffff !important;
2570:2570:            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
2571:2571:            border: 1px solid #e5e7eb !important;
2572:2572:            padding: 1rem;
2573:2573:            border-radius: 0.5rem;
2574:2574:        }
2575:2575:        
2576:2576:        .dark-mode .card-example {
2577:2577:            background-color: #ffffff !important;
2578:2578:            color: #111827 !important;
2579:2579:            border-color: #e5e7eb !important;
2580:2580:        }
2581:2581:        
2582:2582:        .dark-mode .card-example i,
2583:2583:        .dark-mode .card-example h5,
2584:2584:        .dark-mode .card-example .card-title {
2585:2585:            color: #111827 !important;
2586:2586:        }
2587:2587:        
2588:2588:        .dark-mode .card-example-glass {
2589:2589:            background-color: rgba(255, 255, 255, 0.8) !important;
2590:2590:            backdrop-filter: blur(10px) !important;
2591:2591:            -webkit-backdrop-filter: blur(10px) !important;
2592:2592:        }
2593:2593:
2594:2594:        /* List group styles for dark mode */
2595:2595:        .dark-mode .list-group-item {
2596:2596:            background-color: var(--card-bg);
2597:2597:            border-color: var(--border-color);
2598:2598:            color: var(--text-color);
2599:2599:        }
2600:2600:
2601:2601:        .dark-mode .list-group-item-action:hover {
2602:2602:            background-color: var(--hover-bg);
2603:2603:        }
2604:2604:
2605:2605:        .dark-mode .list-group-item.active {
2606:2606:            background-color: var(--primary-color);
2607:2607:            border-color: var(--primary-color);
2608:2608:        }
2609:2609:
2610:2610:        /* Nav pills and tabs for dark mode */
2611:2611:        .dark-mode .nav-pills .nav-link {
2612:2612:            color: var(--text-color);
2613:2613:        }
2614:2614:
2615:2615:        .dark-mode .nav-pills .nav-link.active {
2616:2616:            background-color: var(--primary-color);
2617:2617:            color: #ffffff;
2618:2618:        }
2619:2619:
2620:2620:        .dark-mode .nav-tabs {
2621:2621:            border-color: var(--border-color);
2622:2622:        }
2623:2623:
2624:2624:        .dark-mode .nav-tabs .nav-link {
2625:2625:            color: var(--text-color);
2626:2626:            border-color: transparent;
2627:2627:        }
2628:2628:
2629:2629:        .dark-mode .nav-tabs .nav-link:hover {
2630:2630:            border-color: var(--border-color);
2631:2631:            background-color: var(--hover-bg);
2632:2632:        }
2633:2633:
2634:2634:        .dark-mode .nav-tabs .nav-link.active {
2635:2635:            color: var(--text-color);
2636:2636:            background-color: var(--card-bg);
2637:2637:            border-color: var(--border-color);
2638:2638:            border-bottom-color: var(--card-bg);
2639:2639:        }
2640:2640:
2641:2641:        /* Tooltip styles for dark mode */
2642:2642:        .dark-mode .tooltip .tooltip-inner {
2643:2643:            background-color: var(--dropdown-bg);
2644:2644:            color: var(--text-color);
2645:2645:            border: 1px solid var(--border-color);
2646:2646:        }
2647:2647:
2648:2648:        .dark-mode .tooltip .tooltip-arrow::before {
2649:2649:            border-top-color: var(--dropdown-bg);
2650:2650:        }
2651:2651:
2652:2652:        /* Progress bar styles for dark mode */
2653:2653:        .dark-mode .progress {
2654:2654:            background-color: var(--input-bg);
2655:2655:        }
2656:2656:
2657:2657:        .dark-mode .progress-bar {
2658:2658:            background-color: var(--primary-color);
2659:2659:        }
2660:2660:
2661:2661:        /* Card special styles for dark mode */
2662:2662:        .dark-mode .card-header {
2663:2663:            background-color: rgba(31, 41, 55, 0.6);
2664:2664:            border-bottom-color: var(--border-color);
2665:2665:        }
2666:2666:
2667:2667:        /* Breadcrumb styles for dark mode */
2668:2668:        .dark-mode .breadcrumb {
2669:2669:            background-color: var(--card-bg);
2670:2670:        }
2671:2671:
2672:2672:        .dark-mode .breadcrumb-item {
2673:2673:            color: var(--text-muted);
2674:2674:        }
2675:2675:
2676:2676:        .dark-mode .breadcrumb-item.active {
2677:2677:            color: var(--text-color);
2678:2678:        }
2679:2679:
2680:2680:        .dark-mode .breadcrumb-item + .breadcrumb-item::before {
2681:2681:            color: var(--text-muted);
2682:2682:        }
2683:2683:
2684:2684:        /* Code and pre blocks for dark mode */
2685:2685:        .dark-mode code, .dark-mode pre {
2686:2686:            background-color: #2d3748;
2687:2687:            color: #e2e8f0;
2688:2688:            border-color: var(--border-color);
2689:2689:        }
2690:2690:
2691:2691:        /* Adjustments for specific tenant UI elements */
2692:2692:        .dark-mode .tenant-logo img {
2693:2693:            filter: var(--logo-filter);
2694:2694:        }
2695:2695:
2696:2696:        .dark-mode .tenant-info {
2697:2697:            color: var(--text-color);
2698:2698:        }
2699:2699:
2700:2700:        .dark-mode .stats-card {
2701:2701:            background-color: var(--card-bg);
2702:2702:            border-color: var(--border-color);
2703:2703:            box-shadow: 0 4px 6px var(--shadow-color);
2704:2704:        }
2705:2705:
2706:2706:        .dark-mode .stats-card .stats-icon {
2707:2707:            color: var(--primary-color);
2708:2708:        }
2709:2709:
2710:2710:        .dark-mode .stats-card .stats-value {
2711:2711:            color: var(--text-color);
2712:2712:        }
2713:2713:
2714:2714:        .dark-mode .stats-card .stats-label {
2715:2715:            color: var(--text-muted);
2716:2716:        }
2717:2717:
2718:2718:        .dark-mode .subscription-modal .subscription-content {
2719:2719:            background-color: var(--modal-bg);
2720:2720:            color: var(--text-color);
2721:2721:            border: 1px solid var(--border-color);
2722:2722:            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
2723:2723:        }
2724:2724:
2725:2725:        .dark-mode .subscription-modal .close-modal {
2726:2726:            color: var(--text-color);
2727:2727:            background-color: var(--btn-bg);
2728:2728:        }
2729:2729:
2730:2730:        .dark-mode .subscription-modal .close-modal:hover {
2731:2731:            background-color: var(--btn-hover);
2732:2732:        }
2733:2733:
2734:2734:        .dark-mode .plan-card {
2735:2735:            background-color: var(--card-bg);
2736:2736:            color: var(--text-color);
2737:2737:            border: 1px solid var(--border-color);
2738:2738:        }
2739:2739:
2740:2740:        .dark-mode .plan-card h2 {
2741:2741:            color: var(--accent-color);
2742:2742:        }
2743:2743:
2744:2744:        .dark-mode .plan-card h2 span {
2745:2745:            color: var(--text-secondary);
2746:2746:        }
2747:2747:
2748:2748:        .dark-mode .benefits-list ul li {
2749:2749:            color: var(--text-color);
2750:2750:        }
2751:2751:
2752:2752:        .dark-mode .benefits-list ul li svg {
2753:2753:            fill: var(--accent-color);
2754:2754:        }
2755:2755:
2756:2756:        .dark-mode .button-get-plan a {
2757:2757:            background-color: var(--btn-primary);
2758:2758:            color: white;
2759:2759:        }
2760:2760:
2761:2761:        .dark-mode .button-get-plan a:hover {
2762:2762:            background-color: var(--btn-primary-hover);
2763:2763:        }
2764:2764:
2765:2765:        /* Pagination styles for dark mode */
2766:2766:        .dark-mode .pagination {
2767:2767:            background-color: var(--card-bg);
2768:2768:            border: 1px solid var(--border-color);
2769:2769:        }
2770:2770:
2771:2771:        .dark-mode .pagination label {
2772:2772:            color: var(--text-color);
2773:2773:        }
2774:2774:
2775:2775:        .dark-mode .pagination label:hover:not(.disabled):not(.active) {
2776:2776:            background-color: var(--btn-hover);
2777:2777:        }
2778:2778:
2779:2779:        .dark-mode .pagination label.active {
2780:2780:            background-color: var(--btn-primary);
2781:2781:            color: white;
2782:2782:        }
2783:2783:
2784:2784:        .dark-mode .pagination label.disabled {
2785:2785:            color: var(--text-secondary);
2786:2786:        }
2787:2787:
2788:2788:        .dark-mode .table-display {
2789:2789:            color: var(--text-secondary);
2790:2790:        }
2791:2791:
2792:2792:        /* Tooltip styles for dark mode */
2793:2793:        .dark-mode .tooltip .tooltip-inner {
2794:2794:            background-color: var(--dropdown-bg);
2795:2795:            color: var(--text-color);
2796:2796:            border: 1px solid var(--border-color);
2797:2797:        }
2798:2798:        
2799:2799:        /* Toast notification styles for dark mode */
2800:2800:        .dark-mode .toast {
2801:2801:            background-color: var(--card-bg);
2802:2802:            color: var(--text-color);
2803:2803:            border-color: var(--border-color);
2804:2804:            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
2805:2805:        }
2806:2806:        
2807:2807:        .dark-mode .toast-header {
2808:2808:            background-color: rgba(31, 41, 55, 0.7);
2809:2809:            color: var(--text-color);
2810:2810:            border-bottom-color: var(--border-color);
2811:2811:        }
2812:2812:        
2813:2813:        .dark-mode .toast-body {
2814:2814:            background-color: var(--card-bg);
2815:2815:            color: var(--text-color);
2816:2816:        }
2817:2817:        
2818:2818:        .dark-mode .btn-close {
2819:2819:            filter: invert(1) grayscale(100%) brightness(200%);
2820:2820:        }
2821:2821:        
2822:2822:        .dark-mode #themeToast .toast-body {
2823:2823:            background-color: var(--card-bg);
2824:2824:        }
2825:2825:        
2826:2826:        .dark-mode .toast-container {
2827:2827:            z-index: 1090;
2828:2828:        }
2829:2829:
2830:2830:        /* Dark Mode for Subscription Modal */
2831:2831:        .dark-mode .subscription-modal {
2832:2832:            background-color: rgba(0, 0, 0, 0.85);
2833:2833:        }
2834:2834:
2835:2835:        .dark-mode .subscription-content {
2836:2836:            background-color: var(--buk-card-bg);
2837:2837:            color: var(--buk-text-color);
2838:2838:            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
2839:2839:        }
2840:2840:
2841:2841:        .dark-mode .close-modal {
2842:2842:            color: var(--buk-text-color);
2843:2843:            background-color: rgba(40, 40, 40, 0.7);
2844:2844:        }
2845:2845:
2846:2846:        .dark-mode .close-modal:hover {
2847:2847:            background-color: rgba(60, 60, 60, 0.9);
2848:2848:        }
2849:2849:
2850:2850:        .dark-mode .plan-card {
2851:2851:            background-color: var(--buk-card-bg);
2852:2852:            color: var(--buk-text-color);
2853:2853:            border: 1px solid var(--buk-border-color);
2854:2854:        }
2855:2855:
2856:2856:        .dark-mode .plan-card h2 {
2857:2857:            color: var(--buk-text-color);
2858:2858:        }
2859:2859:
2860:2860:        .dark-mode .plan-card h2 span {
2861:2861:            color: var(--buk-text-color-secondary);
2862:2862:        }
2863:2863:
2864:2864:        .dark-mode .benefits-list {
2865:2865:            color: var(--buk-text-color);
2866:2866:        }
2867:2867:
2868:2868:        .dark-mode .benefits-list ul li {
2869:2869:            color: var(--buk-text-color);
2870:2870:            border-bottom: 1px solid var(--buk-border-color);
2871:2871:        }
2872:2872:
2873:2873:        .dark-mode .benefits-list ul li svg {
2874:2874:            fill: var(--accent-color);
2875:2875:        }
2876:2876:
2877:2877:        .dark-mode .button-get-plan a {
2878:2878:            background-color: var(--accent-color);
2879:2879:            color: var(--buk-card-bg);
2880:2880:        }
2881:2881:
2882:2882:        .dark-mode .button-get-plan a:hover {
2883:2883:            background-color: var(--accent-color-hover);
2884:2884:        }
2885:2885:
2886:2886:        .dark-mode #toastMessage {
2887:2887:            color: inherit;
2888:2888:        }
2889:2889:
2890:2890:        /* Common Toast Styling for Dark Mode */
2891:2891:        .dark-mode .toast {
2892:2892:            background-color: var(--buk-card-bg);
2893:2893:            color: var(--buk-text-color);
2894:2894:            border: 1px solid var(--buk-border-color);
2895:2895:        }
2896:2896:
2897:2897:        .dark-mode .toast-header {
2898:2898:            background-color: rgba(30, 30, 30, 0.9);
2899:2899:            color: var(--buk-text-color);
2900:2900:            border-bottom: 1px solid var(--buk-border-color);
2901:2901:        }
2902:2902:
2903:2903:        .dark-mode .toast .btn-close {
2904:2904:            filter: invert(1) grayscale(100%) brightness(200%);
2905:2905:        }
2906:2906:
2907:2907:        .dark-mode #themeToast {
2908:2908:            border: 1px solid var(--buk-border-color);
2909:2909:        }
2910:2910:
2911:2911:        .dark-mode .toast-container {
2912:2912:            z-index: 9999;
2913:2913:        }
2914:2914:
2915:2915:        /* Font style success modal specific styles */
2916:2916:        .dark-mode #fontStyleSuccessModal .modal-body {
2917:2917:            background-color: var(--buk-dark-bg-secondary);
2918:2918:        }
2919:2919:
2920:2920:        .dark-mode #fontStyleSuccessModal .font-preview {
2921:2921:            background-color: var(--buk-dark-bg-primary);
2922:2922:            border-color: var(--buk-dark-border-color);
2923:2923:            color: var(--buk-light-text-color);
2924:2924:        }
2925:2925:
2926:2926:        .dark-mode #fontStyleSuccessModal .modal-footer .btn-primary {
2927:2927:            background-color: var(--accent-color);
2928:2928:            border-color: var(--accent-color);
2929:2929:            color: #fff;
2930:2930:        }
2931:2931:
2932:2932:        .dark-mode #fontStyleSuccessModal .modal-footer .btn-primary:hover {
2933:2933:            background-color: var(--accent-hover-color, #0056b3);
2934:2934:            border-color: var(--accent-hover-color, #0056b3);
2935:2935:        }
2936:2936:
2937:2937:        /* Glass card style for dashboard components */
2938:2938:        .card-style-glass .card, 
2939:2939:        .card-style-glass .settings-card,
2940:2940:        .card-style-glass .stat-card,
2941:2941:        .card-style-glass .content-card {
2942:2942:            display: block;
2943:2943:            position: relative;
2944:2944:            background-color: #f2f8f9;
2945:2945:            border-radius: 4px;
2946:2946:            padding: 32px 24px;
2947:2947:            margin: 12px;
2948:2948:            text-decoration: none;
2949:2949:            z-index: 0;
2950:2950:            overflow: hidden;
2951:2951:            border: 1px solid #f2f8f9;
2952:2952:            transition: all 0.3s ease;
2953:2953:        }
2954:2954:
2955:2955:        .card-style-glass .card p,
2956:2956:        .card-style-glass .settings-card p,
2957:2957:        .card-style-glass .stat-card p,
2958:2958:        .card-style-glass .content-card p {
2959:2959:            font-size: 17px;
2960:2960:            font-weight: 400;
2961:2961:            line-height: 20px;
2962:2962:            color: #666;
2963:2963:            transition: all 0.3s ease-out;
2964:2964:        }
2965:2965:
2966:2966:        .card-style-glass .card p.small,
2967:2967:        .card-style-glass .settings-card p.small,
2968:2968:        .card-style-glass .stat-card p.small,
2969:2969:        .card-style-glass .content-card p.small {
2970:2970:            font-size: 14px;
2971:2971:        }
2972:2972:
2973:2973:        .card-style-glass .card:before,
2974:2974:        .card-style-glass .settings-card:before,
2975:2975:        .card-style-glass .stat-card:before,
2976:2976:        .card-style-glass .content-card:before {
2977:2977:            content: "";
2978:2978:            position: absolute;
2979:2979:            z-index: -1;
2980:2980:            top: -16px;
2981:2981:            right: -16px;
2982:2982:            background: #00838d;
2983:2983:            height: 32px;
2984:2984:            width: 32px;
2985:2985:            border-radius: 32px;
2986:2986:            transform: scale(1);
2987:2987:            transform-origin: 50% 50%;
2988:2988:            transition: transform 0.25s ease-out;
2989:2989:        }
2990:2990:
2991:2991:        .card-style-glass .card:hover:before,
2992:2992:        .card-style-glass .settings-card:hover:before,
2993:2993:        .card-style-glass .stat-card:hover:before,
2994:2994:        .card-style-glass .content-card:hover:before {
2995:2995:            transform: scale(21);
2996:2996:        }
2997:2997:
2998:2998:        .card-style-glass .card:hover,
2999:2999:        .card-style-glass .settings-card:hover,
3000:3000:        .card-style-glass .stat-card:hover,
3001:3001:        .card-style-glass .content-card:hover {
3002:3002:            border: 1px solid #00838d;
3003:3003:            box-shadow: 0px 0px 999px 999px rgba(255, 255, 255, 0.5);
3004:3004:            z-index: 500;
3005:3005:        }
3006:3006:
3007:3007:        .card-style-glass .card:hover p,
3008:3008:        .card-style-glass .settings-card:hover p,
3009:3009:        .card-style-glass .stat-card:hover p,
3010:3010:        .card-style-glass .content-card:hover p {
3011:3011:            color: rgba(255, 255, 255, 0.8);
3012:3012:        }
3013:3013:
3014:3014:        .card-style-glass .card:hover h3,
3015:3015:        .card-style-glass .settings-card:hover h3,
3016:3016:        .card-style-glass .stat-card:hover h3,
3017:3017:        .card-style-glass .content-card:hover h3 {
3018:3018:            color: #fff;
3019:3019:            transition: all 0.3s ease-out;
3020:3020:        }
3021:3021:
3022:3022:        .card-style-glass .go-corner {
3023:3023:            display: flex;
3024:3024:            align-items: center;
3025:3025:            justify-content: center;
3026:3026:            position: absolute;
3027:3027:            width: 32px;
3028:3028:            height: 32px;
3029:3029:            overflow: hidden;
3030:3030:            top: 0;
3031:3031:            right: 0;
3032:3032:            background-color: #00838d;
3033:3033:            border-radius: 0 4px 0 32px;
3034:3034:            opacity: 0.7;
3035:3035:            transition: opacity 0.3s linear;
3036:3036:        }
3037:3037:
3038:3038:        .card-style-glass .card:hover .go-corner,
3039:3039:        .card-style-glass .settings-card:hover .go-corner,
3040:3040:        .card-style-glass .stat-card:hover .go-corner,
3041:3041:        .card-style-glass .content-card:hover .go-corner {
3042:3042:            opacity: 1;
3043:3043:        }
3044:3044:
3045:3045:        .card-style-glass .go-arrow {
3046:3046:            margin-top: -4px;
3047:3047:            margin-right: -4px;
3048:3048:            color: white;
3049:3049:            font-family: courier, sans;
3050:3050:        }
3051:3051:
3052:3052:        /* Dark mode specific styles for glass cards */
3053:3053:        body.dark-mode .card-style-glass .card,
3054:3054:        body.dark-mode .card-style-glass .settings-card,
3055:3055:        body.dark-mode .card-style-glass .stat-card,
3056:3056:        body.dark-mode .card-style-glass .content-card {
3057:3057:            background-color: #1a1a1a;
3058:3058:            border-color: #2d2d2d;
3059:3059:        }
3060:3060:
3061:3061:        body.dark-mode .card-style-glass .card p,
3062:3062:        body.dark-mode .card-style-glass .settings-card p,
3063:3063:        body.dark-mode .card-style-glass .stat-card p,
3064:3064:        body.dark-mode .card-style-glass .content-card p {
3065:3065:            color: #a0aec0;
3066:3066:        }
3067:3067:
3068:3068:        body.dark-mode .card-style-glass .card:hover,
3069:3069:        body.dark-mode .card-style-glass .settings-card:hover,
3070:3070:        body.dark-mode .card-style-glass .stat-card:hover,
3071:3071:        body.dark-mode .card-style-glass .content-card:hover {
3072:3072:            border-color: #00838d;
3073:3073:            box-shadow: 0px 0px 999px 999px rgba(0, 0, 0, 0.5);
3074:3074:        }
3075:3075:
3076:3076:        /* Glass card style for dashboard components */
3077:3077:        body[data-card-style="glass"] .dashboard-card,
3078:3078:        .card-style-glass .dashboard-card {
3079:3079:            display: block;
3080:3080:            position: relative;
3081:3081:            background-color: #f2f8f9;
3082:3082:            border-radius: 4px;
3083:3083:            padding: 32px 24px;
3084:3084:            margin: 12px;
3085:3085:            text-decoration: none;
3086:3086:            z-index: 0;
3087:3087:            overflow: hidden;
3088:3088:            border: 1px solid #f2f8f9;
3089:3089:            transition: all 0.3s ease;
3090:3090:        }
3091:3091:
3092:3092:        body[data-card-style="glass"] .dashboard-card p,
3093:3093:        .card-style-glass .dashboard-card p {
3094:3094:            font-size: 17px;
3095:3095:            font-weight: 400;
3096:3096:            line-height: 20px;
3097:3097:            color: #666;
3098:3098:            transition: all 0.3s ease-out;
3099:3099:        }
3100:3100:
3101:3101:        body[data-card-style="glass"] .dashboard-card p.small,
3102:3102:        .card-style-glass .dashboard-card p.small {
3103:3103:            font-size: 14px;
3104:3104:        }
3105:3105:
3106:3106:        body[data-card-style="glass"] .dashboard-card:before,
3107:3107:        .card-style-glass .dashboard-card:before {
3108:3108:            content: "";
3109:3109:            position: absolute;
3110:3110:            z-index: -1;
3111:3111:            top: -16px;
3112:3112:            right: -16px;
3113:3113:            background: #00838d;
3114:3114:            height: 32px;
3115:3115:            width: 32px;
3116:3116:            border-radius: 32px;
3117:3117:            transform: scale(1);
3118:3118:            transform-origin: 50% 50%;
3119:3119:            transition: transform 0.25s ease-out;
3120:3120:        }
3121:3121:
3122:3122:        body[data-card-style="glass"] .dashboard-card:hover:before,
3123:3123:        .card-style-glass .dashboard-card:hover:before {
3124:3124:            transform: scale(21);
3125:3125:        }
3126:3126:
3127:3127:        body[data-card-style="glass"] .dashboard-card:hover,
3128:3128:        .card-style-glass .dashboard-card:hover {
3129:3129:            border: 1px solid #00838d;
3130:3130:            box-shadow: 0px 0px 999px 999px rgba(255, 255, 255, 0.5);
3131:3131:            z-index: 500;
3132:3132:        }
3133:3133:
3134:3134:        body[data-card-style="glass"] .dashboard-card:hover p,
3135:3135:        .card-style-glass .dashboard-card:hover p {
3136:3136:            color: rgba(255, 255, 255, 0.8);
3137:3137:        }
3138:3138:
3139:3139:        body[data-card-style="glass"] .dashboard-card:hover h3,
3140:3140:        .card-style-glass .dashboard-card:hover h3 {
3141:3141:            color: #fff;
3142:3142:            transition: all 0.3s ease-out;
3143:3143:        }
3144:3144:
3145:3145:        body[data-card-style="glass"] .dashboard-card .go-corner,
3146:3146:        .card-style-glass .dashboard-card .go-corner {
3147:3147:            display: flex;
3148:3148:            align-items: center;
3149:3149:            justify-content: center;
3150:3150:            position: absolute;
3151:3151:            width: 32px;
3152:3152:            height: 32px;
3153:3153:            overflow: hidden;
3154:3154:            top: 0;
3155:3155:            right: 0;
3156:3156:            background-color: #00838d;
3157:3157:            border-radius: 0 4px 0 32px;
3158:3158:            opacity: 0.7;
3159:3159:            transition: opacity 0.3s linear;
3160:3160:        }
3161:3161:
3162:3162:        body[data-card-style="glass"] .dashboard-card:hover .go-corner,
3163:3163:        .card-style-glass .dashboard-card:hover .go-corner {
3164:3164:            opacity: 1;
3165:3165:        }
3166:3166:
3167:3167:        body[data-card-style="glass"] .dashboard-card .go-arrow,
3168:3168:        .card-style-glass .dashboard-card .go-arrow {
3169:3169:            margin-top: -4px;
3170:3170:            margin-right: -4px;
3171:3171:            color: white;
3172:3172:            font-family: courier, sans;
3173:3173:        }
3174:3174:
3175:3175:        /* Dark mode specific styles for glass dashboard cards */
3176:3176:        body.dark-mode[data-card-style="glass"] .dashboard-card,
3177:3177:        body.dark-mode .card-style-glass .dashboard-card {
3178:3178:            background-color: #1a1a1a;
3179:3179:            border-color: #2d2d2d;
3180:3180:        }
3181:3181:
3182:3182:        body.dark-mode[data-card-style="glass"] .dashboard-card p,
3183:3183:        body.dark-mode .card-style-glass .dashboard-card p {
3184:3184:            color: #a0aec0;
3185:3185:        }
3186:3186:
3187:3187:        body.dark-mode[data-card-style="glass"] .dashboard-card:hover,
3188:3188:        body.dark-mode .card-style-glass .dashboard-card:hover {
3189:3189:            border-color: #00838d;
3190:3190:            box-shadow: 0px 0px 999px 999px rgba(0, 0, 0, 0.5);
3191:3191:        }
3192:3192:
3193:3193:        /* Regular card styles (non-dashboard) */
3194:3194:        .card-style-glass .card:not(.dashboard-card),
3195:3195:        .card-style-glass .settings-card:not(.dashboard-card),
3196:3196:        .card-style-glass .stat-card:not(.dashboard-card),
3197:3197:        .card-style-glass .content-card:not(.dashboard-card) {
3198:3198:            background: rgba(255, 255, 255, 0.7);
3199:3199:            backdrop-filter: blur(10px);
3200:3200:            -webkit-backdrop-filter: blur(10px);
3201:3201:            border: 1px solid rgba(255, 255, 255, 0.3);
3202:3202:            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
3203:3203:        }
3204:3204:
3205:3205:        /* Dashboard card hover effects */
3206:3206:        .dashboard-card {
3207:3207:            transition: all 0.3s ease;
3208:3208:        }
3209:3209:
3210:3210:        .dashboard-card:hover {
3211:3211:            transform: translateY(-5px);
3212:3212:            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
3213:3213:        }
3214:3214:
3215:3215:        body.dark-mode .dashboard-card:hover {
3216:3216:            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.4);
3217:3217:        }
3218:3218:
3219:3219:        /* Card style variations - without hover effects */
3220:3220:        .card-rounded {
3221:3221:            border-radius: 1rem !important;
3222:3222:            overflow: hidden;
3223:3223:        }
3224:3224:        
3225:3225:        .card-square {
3226:3226:            border-radius: 0 !important;
3227:3227:            overflow: hidden;
3228:3228:        }
3229:3229:        
3230:3230:        .card-glass {
3231:3231:            border-radius: 0.5rem !important;
3232:3232:            background-color: rgba(255, 255, 255, 0.8) !important;
3233:3233:            backdrop-filter: blur(10px);
3234:3234:            -webkit-backdrop-filter: blur(10px);
3235:3235:            border: 1px solid rgba(255, 255, 255, 0.2);
3236:3236:            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
3237:3237:        }
3238:3238:        
3239:3239:        body.dark-mode .card-glass {
3240:3240:            background-color: rgba(30, 41, 59, 0.8) !important;
3241:3241:            border: 1px solid rgba(255, 255, 255, 0.1);
3242:3242:        }
3243:3243:
3244:3244:        /* Dashboard-specific hover effects */
3245:3245:        body[data-page="dashboard"] .card {
3246:3246:            transition: all 0.3s ease;
3247:3247:        }
3248:3248:
3249:3249:        body[data-page="dashboard"] .card:hover {
3250:3250:            transform: translateY(-5px);
3251:3251:            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
3252:3252:        }
3253:3253:
3254:3254:        body.dark-mode[data-page="dashboard"] .card:hover {
3255:3255:            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.4);
3256:3256:        }
3257:3257:
3258:3258:        /* Card style variations - without hover effects */
3259:3259:        .card-rounded {
3260:3260:            border-radius: 1rem !important;
3261:3261:            overflow: hidden;
3262:3262:        }
3263:3263:        
3264:3264:        .card-square {
3265:3265:            border-radius: 0 !important;
3266:3266:            overflow: hidden;
3267:3267:        }
3268:3268:        
3269:3269:        .card-glass {
3270:3270:            border-radius: 0.5rem !important;
3271:3271:            background-color: rgba(255, 255, 255, 0.8) !important;
3272:3272:            backdrop-filter: blur(10px);
3273:3273:            -webkit-backdrop-filter: blur(10px);
3274:3274:            border: 1px solid rgba(255, 255, 255, 0.2);
3275:3275:            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
3276:3276:        }
3277:3277:
3278:3278:        /* Remove hover effect specifically for Reports dropdown */
3279:3279:        .sidebar .nav-item.dropdown .nav-link.dropdown-toggle:hover {
3280:3280:            background: transparent;
3281:3281:            color: var(--sidebar-text-color);
3282:3282:        }
3283:3283:        
3284:3284:        .sidebar .nav-item.dropdown .nav-link.dropdown-toggle:hover i {
3285:3285:            color: var(--sidebar-icon-color);
3286:3286:        }
3287:3287:
3288:3288:        body.dark-mode .sidebar .nav-link.dropdown-toggle:hover .dropdown-icon,
3289:3289:        body.dark-mode .sidebar .nav-link.dropdown-toggle.show .dropdown-icon {
3290:3290:            color: #ffffff;
3291:3291:        }
3292:3292:        
3293:3293:        /* Remove hover effect for Reports dropdown in dark mode */
3294:3294:        body.dark-mode .sidebar .nav-item.dropdown .nav-link.dropdown-toggle:hover {
3295:3295:            background: transparent;
3296:3296:            color: var(--sidebar-text-color);
3297:3297:        }
3298:3298:        
3299:3299:        body.dark-mode .sidebar .nav-item.dropdown .nav-link.dropdown-toggle:hover i {
3300:3300:            color: var(--sidebar-icon-color);
3301:3301:        }
3302:3302:
3303:3303:        /* Sidebar Upgrade Button Styles */
3304:3304:        .sidebar-upgrade-btn {
3305:3305:            background-color: #FF8C00 !important; /* Dark Orange */
3306:3306:            color: white !important;
3307:3307:            border-color: #FF8C00 !important;
3308:3308:            width: calc(100% - 20px) !important; /* Added margin space */
3309:3309:            margin-left: 10px !important; /* Left margin */
3310:3310:            margin-right: 10px !important; /* Right margin */
3311:3311:            text-align: center;
3312:3312:            border-radius: 5px !important; /* 5px radius on all corners */
3313:3313:            padding: 10px !important;
3314:3314:            font-weight: 600;
3315:3315:            letter-spacing: 0.2px;
3316:3316:            display: flex;
3317:3317:            align-items: center;
3318:3318:            justify-content: center;
3319:3319:            margin-bottom: 10px;
3320:3320:        }
3321:3321:        
3322:3322:        .sidebar-upgrade-btn:hover,
3323:3323:        .sidebar-upgrade-btn:active,
3324:3324:        .sidebar-upgrade-btn:focus {
3325:3325:            background-color: #FF8C00 !important; /* Keep the same dark orange */
3326:3326:            color: white !important;
3327:3327:            border-color: #FF8C00 !important;
3328:3328:        }
3329:3329:        
3330:3330:        /* Dark mode support */
3331:3331:        body.dark-mode .sidebar-upgrade-btn {
3332:3332:            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
3333:3333:        }
3334:3334:
3335:3335:        /* Premium badge styles */
3336:3336:        .premium-badge {
3337:3337:            background-color: #ffeccc !important;
3338:3338:            color: black !important;
3339:3339:            border-color: #FF8C00 !important;
3340:3340:        }
3341:3341:        
3342:3342:        .premium-badge i.fa-crown {
3343:3343:            color: #FF8C00 !important;
3344:3344:        }
3345:3345:        
3346:3346:        /* Ultimate badge styles */
3347:3347:        .premium-badge.ultimate-badge {
3348:3348:            background-color: #e6eaff !important;
3349:3349:            color: black !important;
3350:3350:            border-color: #4361ee !important;
3351:3351:        }
3352:3352:        
3353:3353:        .premium-badge i.fa-star {
3354:3354:            color: #4361ee !important;
3355:3355:        }
3356:3356:    </style>
3357:3357:    @stack('styles')
3358:3358:</head>
3359:3359:<body class="{{ isset($settings) && $settings->dark_mode ? 'dark-mode' : '' }} {{ request()->routeIs('tenant.reports.*') ? 'reports-page' : '' }}" 
3360:3360:      data-card-style="{{ isset($settings) && $settings->card_style ? $settings->card_style : 'square' }}">
3361:3361:    <div x-data="{ isSidebarOpen: false }" class="layout-wrapper">
3362:3362:    <!-- Sidebar -->
3363:3363:        <div class="sidebar" :class="{ 'show': isSidebarOpen }">
3364:3364:            <div class="sidebar-content">
3365:3365:                <div class="logo-container">
3366:3366:                    <img src="{{ asset('assets/images/logo.png') }}" 
3367:3367:                         alt="BukSkwela Logo">
3368:3368:                </div>
3369:3369:                
3370:3370:                <ul class="nav flex-column px-3 flex-grow-1">
3371:3371:                    @if(auth()->guard('student')->check())
3372:3372:                    <!-- Student Menu Items -->
3373:3373:                    <li class="nav-item">
3374:3374:                        <a class="nav-link {{ request()->routeIs('tenant.student.dashboard') ? 'active' : '' }}" 
3375:3375:                           href="{{ route('tenant.student.dashboard', ['tenant' => tenant('id')]) }}">
3376:3376:                            <i class="fas fa-home"></i> Dashboard
3377:3377:                        </a>
3378:3378:                    </li>
3379:3379:                    <li class="nav-item">
3380:3380:                        <a class="nav-link {{ request()->routeIs('tenant.student.enrollment') ? 'active' : '' }}" 
3381:3381:                           href="{{ route('tenant.student.enrollment', ['tenant' => tenant('id')]) }}">
3382:3382:                            <i class="fas fa-tasks"></i> <span>Enrollment</span>
3383:3383:                        </a>
3384:3384:                    </li>
3385:3385:                    @else
3386:3386:                    <!-- Admin/Staff Menu Items -->
3387:3387:                    <li class="nav-item">
3388:3388:                        <a class="nav-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}" 
3389:3389:                           href="{{ route('tenant.dashboard', ['tenant' => tenant('id')]) }}">
3390:3390:                            <i class="fas fa-home"></i> Dashboard
3391:3391:                        </a>
3392:3392:                    </li>
3393:3393:                    <li class="nav-item">
3394:3394:                        <a class="nav-link {{ request()->routeIs('tenant.students.*') ? 'active' : '' }}" 
3395:3395:                           href="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}">
3396:3396:                            <i class="fas fa-users"></i> <span>Students</span>
3397:3397:                        </a>
3398:3398:                    </li>
3399:3399:                    <li class="nav-item">
3400:3400:                        <a class="nav-link {{ request()->routeIs('tenant.staff.*') ? 'active' : '' }}" 
3401:3401:                           href="{{ route('tenant.staff.index', ['tenant' => tenant('id')]) }}">
3402:3402:                            <i class="fas fa-chalkboard-teacher"></i> <span>Staff</span>
3403:3403:                        </a>
3404:3404:                    </li>
3405:3405:                    <li class="nav-item">
3406:3406:                        <a class="nav-link {{ request()->routeIs('tenant.courses.*') ? 'active' : '' }}" 
3407:3407:                           href="{{ route('tenant.courses.index', ['tenant' => tenant('id')]) }}">
3408:3408:                            <i class="fas fa-book"></i> <span>Courses</span>
3409:3409:                        </a>
3410:3410:                    </li>
3411:3411:                    <li class="nav-item">
3412:3412:                        <a class="nav-link {{ request()->routeIs('tenant.admin.requirements.*') ? 'active' : '' }}" 
3413:3413:                           href="{{ route('tenant.admin.requirements.index', ['tenant' => tenant('id')]) }}">
3414:3414:                            <i class="fas fa-clipboard-list"></i> <span>Requirements</span>
3415:3415:                        </a>
3416:3416:                    </li>
3417:3417:                   
3418:3418:                    @php
3419:3419:                        // Get current URL to extract tenant ID
3420:3420:                        $url = request()->url();
3421:3421:                        preg_match('/^https?:\/\/([^\.]+)\./', $url, $matches);
3422:3422:                        $tenantDomain = $matches[1] ?? null;
3423:3423:                        
3424:3424:                        // Get tenant from domain or tenant helper
3425:3425:                        if ($tenantDomain) {
3426:3426:                            $currentTenant = \App\Models\Tenant::where('id', $tenantDomain)->first();
3427:3427:                        } else {
3428:3428:                            $tenantId = tenant('id') ?? null;
3429:3429:                            $currentTenant = $tenantId ? \App\Models\Tenant::find($tenantId) : null;
3430:3430:                        }
3431:3431:                        
3432:3432:                        $isPremium = $currentTenant && $currentTenant->subscription_plan === 'premium';
3433:3433:                        $isUltimate = $currentTenant && $currentTenant->subscription_plan === 'ultimate';
3434:3434:                    @endphp
3435:3435:
3436:3436:                    <!-- Reports Dropdown -->
3437:3437:                    <li class="nav-item dropdown">
3438:3438:                        <a class="nav-link dropdown-toggle {{ request()->routeIs('tenant.reports.*') ? 'active' : '' }}" 
3439:3439:                           href="#"
3440:3440:                           data-bs-toggle="{{ $isPremium || $isUltimate ? 'dropdown' : 'modal' }}" 
3441:3441:                           data-bs-target="{{ $isPremium || $isUltimate ? '' : '#premiumFeaturesModal' }}"
3442:3442:                           aria-expanded="{{ request()->routeIs('tenant.reports.*') ? 'true' : 'false' }}">
3443:3443:                            <div class="nav-content">
3444:3444:                                <i class="fas fa-chart-bar"></i>
3445:3445:                                <span>Reports</span>
3446:3446:                                @if(!$isPremium && !$isUltimate)
3447:3447:                                    <i class="fas fa-crown text-warning ms-2" style="font-size: 0.75rem;"></i>
3448:3448:                                @endif
3449:3449:                            </div>
3450:3450:                            <i class="fas fa-chevron-down dropdown-icon"></i>
3451:3451:                        </a>
3452:3452:                        @if($isPremium || $isUltimate)
3453:3453:                        <div class="dropdown-menu {{ request()->routeIs('tenant.reports.*') ? 'show' : '' }}">
3454:3454:                            <a class="dropdown-item {{ request()->routeIs('tenant.reports.students') || request()->routeIs('tenant.reports.students.*') ? 'active' : '' }}" 
3455:3455:                               href="{{ route('tenant.reports.students', ['tenant' => tenant('id')]) }}">
3456:3456:                                <i class="fas fa-user-graduate"></i>
3457:3457:                                <span>Student Reports</span>
3458:3458:                            </a>
3459:3459:                            <a class="dropdown-item {{ request()->routeIs('tenant.reports.staff') || request()->routeIs('tenant.reports.staff.*') ? 'active' : '' }}" 
3460:3460:                               href="{{ route('tenant.reports.staff', ['tenant' => tenant('id')]) }}">
3461:3461:                                <i class="fas fa-user-tie"></i>
3462:3462:                                <span>Staff Reports</span>
3463:3463:                            </a>
3464:3464:                            <a class="dropdown-item {{ request()->routeIs('tenant.reports.courses') || request()->routeIs('tenant.reports.courses.*') ? 'active' : '' }}" 
3465:3465:                               href="{{ route('tenant.reports.courses', ['tenant' => tenant('id')]) }}">
3466:3466:                                <i class="fas fa-book-open"></i>
3467:3467:                                <span>Course Reports</span>
3468:3468:                            </a>
3469:3469:                            <a class="dropdown-item {{ request()->routeIs('tenant.reports.requirements') || request()->routeIs('tenant.reports.requirements.*') ? 'active' : '' }}" 
3470:3470:                               href="{{ route('tenant.reports.requirements', ['tenant' => tenant('id')]) }}">
3471:3471:                                <i class="fas fa-clipboard-check"></i>
3472:3472:                                <span>Requirements Reports</span>
3473:3473:                            </a>
3474:3474:                        </div>
3475:3475:                        @else
3476:3476:                        <div class="dropdown-menu">
3477:3477:                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#premiumFeaturesModal">
3478:3478:                                <i class="fas fa-user-graduate"></i>
3479:3479:                                <span>Student Reports</span>
3480:3480:                                <i class="fas fa-crown text-warning ms-2" style="font-size: 0.75rem;"></i>
3481:3481:                            </a>
3482:3482:                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#premiumFeaturesModal">
3483:3483:                                <i class="fas fa-user-tie"></i>
3484:3484:                                <span>Staff Reports</span>
3485:3485:                                <i class="fas fa-crown text-warning ms-2" style="font-size: 0.75rem;"></i>
3486:3486:                            </a>
3487:3487:                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#premiumFeaturesModal">
3488:3488:                                <i class="fas fa-book-open"></i>
3489:3489:                                <span>Course Reports</span>
3490:3490:                                <i class="fas fa-crown text-warning ms-2" style="font-size: 0.75rem;"></i>
3491:3491:                            </a>
3492:3492:                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#premiumFeaturesModal">
3493:3493:                                <i class="fas fa-clipboard-check"></i>
3494:3494:                                <span>Requirements Reports</span>
3495:3495:                                <i class="fas fa-crown text-warning ms-2" style="font-size: 0.75rem;"></i>
3496:3496:                            </a>
3497:3497:                        </div>
3498:3498:                        @endif
3499:3499:                    </li>
3500:3500:                    @endif
3501:3501:                </ul>
3502:3502:
3503:3503:                <!-- Upgrade to Pro Button -->
3504:3504:                <div class="mt-auto">
3505:3505:                    @php
3506:3506:                        // Get current URL to extract tenant ID
3507:3507:                        $url = request()->url();
3508:3508:                        preg_match('/^https?:\/\/([^\.]+)\./', $url, $matches);
3509:3509:                        $tenantDomain = $matches[1] ?? null;
3510:3510:                        
3511:3511:                        // Get tenant from domain or tenant helper
3512:3512:                        if ($tenantDomain) {
3513:3513:                            $currentTenant = \App\Models\Tenant::where('id', $tenantDomain)->first();
3514:3514:                        } else {
3515:3515:                            $tenantId = tenant('id') ?? null;
3516:3516:                            $currentTenant = $tenantId ? \App\Models\Tenant::find($tenantId) : null;
3517:3517:                        }
3518:3518:                        
3519:3519:                        $isPremium = $currentTenant && $currentTenant->subscription_plan === 'premium';
3520:3520:                        $isUltimate = $currentTenant && $currentTenant->subscription_plan === 'ultimate';
3521:3521:                    @endphp
3522:3522:                    
3523:3523:                    @if(!$isPremium && !$isUltimate)
3524:3524:                        <a href="#" class="btn btn-sm btn-block sidebar-upgrade-btn" data-bs-toggle="modal" data-bs-target="#sidebarPremiumModal">
3525:3525:                            <i class="fas fa-crown me-2"></i>Upgrade to Premium
3526:3526:                        </a>
3527:3527:                    @elseif($isPremium)
3528:3528:                        <div class="premium-badge w-100 mb-2 d-flex align-items-center justify-content-center" style="background-color: #ffeccc !important; color: #000000 !important; border-color: #FF8C00 !important;">
3529:3529:                            <i class="fas fa-crown" style="color: #FF8C00 !important;"></i>
3530:3530:                            <span style="color: #000000 !important;">Premium</span>
3531:3531:                        </div>
3532:3532:                    @elseif($isUltimate)
3533:3533:                        <div class="premium-badge w-100 mb-2 d-flex align-items-center justify-content-center" style="background-color: #e6eaff !important; color: #000000 !important; border-color: #4361ee !important;">
3534:3534:                            <i class="fas fa-star" style="color: #4361ee !important;"></i>
3535:3535:                            <span style="color: #000000 !important;">Ultimate</span>
3536:3536:                        </div>
3537:3537:                    @if(!auth()->guard('student')->check())
3538:3538:                        @if(!$isPremium && !$isUltimate)
3539:3539:                            <a href="#" class="btn btn-sm btn-outline-warning w-100 mb-2 d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#sidebarPremiumModal">
3540:3540:                                <i class="fas fa-crown me-1"></i>
3541:3541:                                <small>Upgrade to Premium</small>
3542:3542:                            </a>
3543:3543:                        @elseif($isPremium)
3544:3544:                            <div class="premium-badge w-100 mb-2 d-flex align-items-center justify-content-center">
3545:3545:                                <i class="fas fa-crown text-warning me-1"></i>
3546:3546:                                <small>Premium Account</small>
3547:3547:                            </div>
3548:3548:                        @elseif($isUltimate)
3549:3549:                            <div class="premium-badge w-100 mb-2 d-flex align-items-center justify-content-center" style="background-color: #e6eaff; color: #4361ee;">
3550:3550:                                <i class="fas fa-star text-primary me-1"></i>
3551:3551:                                <small>Ultimate Account</small>
3552:3552:                            </div>
3553:3553:                        @endif
3554:3554:                    @endif
3555:3555:                </div>
3556:3556:            </div>
3557:3557:        </div>
3558:3558:
3559:3559:        <!-- Content Wrapper -->
3560:3560:        <div class="content-wrapper">
3561:3561:    <!-- Top Navbar -->
3562:3562:            <nav class="navbar navbar-expand-lg navbar-light top-navbar">
3563:3563:        <div class="container-fluid">
3564:3564:                    <!-- Mobile menu button -->
3565:3565:                    <button @click="isSidebarOpen = !isSidebarOpen" 
3566:3566:                            class="btn btn-link d-lg-none">
3567:3567:                        <i class="fas fa-bars"></i>
3568:3568:                    </button>
3569:3569:
3570:3570:                    <div class="ms-auto d-flex align-items-center">
3571:3571:                        <!-- Premium Indicator -->
3572:3572:                        @php
3573:3573:                            // Get current URL to extract tenant ID
3574:3574:                            $url = request()->url();
3575:3575:                            preg_match('/^https?:\/\/([^\.]+)\./', $url, $matches);
3576:3576:                            $tenantDomain = $matches[1] ?? null;
3577:3577:                            
3578:3578:                            // Get tenant from domain or tenant helper
3579:3579:                            if ($tenantDomain) {
3580:3580:                                $currentTenant = \App\Models\Tenant::where('id', $tenantDomain)->first();
3581:3581:                            } else {
3582:3582:                                $tenantId = tenant('id') ?? null;
3583:3583:                                $currentTenant = $tenantId ? \App\Models\Tenant::find($tenantId) : null;
3584:3584:                            }
3585:3585:                            
3586:3586:                            $isPremium = $currentTenant && $currentTenant->subscription_plan === 'premium';
3587:3587:                            $isUltimate = $currentTenant && $currentTenant->subscription_plan === 'ultimate';
3588:3588:                        @endphp
3589:3589:
3590:3590:                        @if($isPremium)
3591:3591:                            <div class="premium-badge me-3" style="background-color: #ffeccc; color: #FF8C00;">
3592:3592:                                <i class="fas fa-crown" style="color: #FF8C00;"></i>
3593:3593:                                <span>Premium</span>
3594:3594:                            </div>
3595:3595:                        @elseif($isUltimate)
3596:3596:                            <div class="premium-badge me-3" style="background-color: #e6eaff; color: #4361ee;">
3597:3597:                                <i class="fas fa-star" style="color: #4361ee;"></i>
3598:3598:                                <span>Ultimate</span>
3599:3599:                            </div>
3600:3600:                        @if(!auth()->guard('student')->check())
3601:3601:                            @if($isPremium)
3602:3602:                                <div class="premium-badge me-3">
3603:3603:                                    <i class="fas fa-crown"></i>
3604:3604:                                    <span>Premium</span>
3605:3605:                                </div>
3606:3606:                            @elseif($isUltimate)
3607:3607:                                <div class="premium-badge me-3" style="background-color: #e6eaff; color: #4361ee;">
3608:3608:                                    <i class="fas fa-star"></i>
3609:3609:                                    <span>Ultimate</span>
3610:3610:                                </div>
3611:3611:                            @endif
3612:3612:                        @endif
3613:3613:
3614:3614:                        <!-- Dark Mode Toggle -->
3615:3615:                        <div class="navbar-dark-mode-toggle me-3">
3616:3616:                            <label class="theme-switch" title="Toggle Dark Mode">
3617:3617:                                <input type="checkbox" id="navbarDarkModeToggle">
3618:3618:                                <span class="theme-slider">
3619:3619:                                    <i class="fas fa-sun theme-slider-icon light-icon"></i>
3620:3620:                                    <i class="fas fa-moon theme-slider-icon dark-icon"></i>
3621:3621:                                </span>
3622:3622:                            </label>
3623:3623:                        </div>
3624:3624:
3625:3625:                        <!-- Admin Avatar with Dropdown -->
3626:3626:                        <div class="dropdown">
3627:3627:                            <button class="btn p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
3628:3628:                                <div class="user-avatar-container">
3629:3629:                                    <img src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff" 
3630:3630:                                         alt="User" 
3631:3631:                                         class="user-avatar"
3632:3632:                                         style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
3633:3633:                                </div>
3634:3634:                            </button>
3635:3635:                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
3636:3636:                                <div class="dropdown-header">
3637:3637:                                    @if(auth()->guard('student')->check())
3638:3638:                                    <strong>{{ auth()->guard('student')->user()->name ?? session('student_name', 'Student') }}</strong>
3639:3639:                                    <p class="mb-0 text-muted small">{{ auth()->guard('student')->user()->email ?? session('student_email', 'No email') }}</p>
3640:3640:                                    <span class="badge bg-info mt-1">Student</span>
3641:3641:                                    @else
3642:3642:                                    <strong>{{ Auth::guard('admin')->user()->name ?? 'User' }}</strong>
3643:3643:                                    <p class="mb-0 text-muted small">{{ Auth::guard('admin')->user()->email ?? 'No email' }}</p>
3644:3644:                                    @if($isPremium)
3645:3645:                                        <span class="badge mt-1" style="background-color: #FF8C00; color: white;">
3646:3646:                                            <i class="fas fa-crown"></i> Premium
3647:3647:                                        </span>
3648:3648:                                    @elseif($isUltimate)
3649:3649:                                        <span class="badge mt-1" style="background-color: #4361ee;">
3650:3650:                                            <i class="fas fa-star"></i> Ultimate
3651:3651:                                        </span>
3652:3652:                                    @endif
3653:3653:                                    @endif
3654:3654:                                </div>
3655:3655:                                <div class="dropdown-divider"></div>
3656:3656:                                <a class="dropdown-item" href="{{ $currentTenant ? route('profile.index', ['tenant' => $currentTenant->id]) : '#' }}">
3657:3657:                                    <i class="fas fa-user"></i>
3658:3658:                                    <span>Profile</span>
3659:3659:                                </a>
3660:3660:                                <a class="dropdown-item" href="{{ $currentTenant ? route('tenant.settings', ['tenant' => $currentTenant->id]) : '#' }}">
3661:3661:                                    <i class="fas fa-cog"></i>
3662:3662:                                    <span>Settings</span>
3663:3663:                                </a>
3664:3664:                                <div class="dropdown-divider"></div>
3665:3665:                                @if(auth()->guard('student')->check())
3666:3666:                                <form id="logout-form" action="{{ route('tenant.student.logout') }}" method="POST" style="display: none;">
3667:3667:                                    @csrf
3668:3668:                                </form>
3669:3669:                                @else
3670:3670:                                <form id="logout-form" action="{{ route('tenant.logout') }}" method="POST" style="display: none;">
3671:3671:                                    @csrf
3672:3672:                                </form>
3673:3673:                                @endif
3674:3674:                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
3675:3675:                                    <i class="fas fa-sign-out-alt"></i>
3676:3676:                                    <span>Logout</span>
3677:3677:                                </a>
3678:3678:                            </div>
3679:3679:                        </div>
3680:3680:                    </div>
3681:3681:                </div>
3682:3682:            </nav>
3683:3683:
3684:3684:    <!-- Add this right after the nav to debug tenant info -->
3685:3685:    @if(config('app.debug'))
3686:3686:        <div class="d-none">
3687:3687:            @php
3688:3688:                dump([
3689:3689:                    'url' => $url ?? null,
3690:3690:                    'tenant_domain_from_url' => $tenantDomain ?? null,
3691:3691:                    'tenant_id_from_helper' => tenant('id') ?? null,
3692:3692:                    'current_tenant' => $currentTenant ?? null,
3693:3693:                    'is_premium' => $isPremium ?? false,
3694:3694:                    'subscription_plan' => $currentTenant->subscription_plan ?? null
3695:3695:                ]);
3696:3696:            @endphp
3697:3697:        </div>
3698:3698:    @endif
3699:3699:
3700:3700:    <!-- Main Content -->
3701:3701:            <main class="main-content">
3702:3702:        @yield('content')
3703:3703:            </main>
3704:3704:        </div>
3705:3705:    </div>
3706:3706:
3707:3707:    <!-- Bootstrap Bundle with Popper -->
3708:3708:    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
3709:3709:    
3710:3710:    <!-- Initialize Bootstrap components -->
3711:3711:    <script>
3712:3712:        document.addEventListener('DOMContentLoaded', function() {
3713:3713:            // Handle payment method change in sidebar modal
3714:3714:            document.getElementById('sidebar_payment_method')?.addEventListener('change', function() {
3715:3715:                // Hide all payment details
3716:3716:                document.querySelectorAll('#sidebarPremiumModal .payment-details').forEach(el => {
3717:3717:                    el.classList.add('d-none');
3718:3718:                });
3719:3719:                
3720:3720:                // Show selected payment method details
3721:3721:                const method = this.value;
3722:3722:                if (method) {
3723:3723:                    document.getElementById('sidebar_' + method + 'Details')?.classList.remove('d-none');
3724:3724:                }
3725:3725:            });
3726:3726:            
3727:3727:            // Initialize all dropdowns
3728:3728:            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
3729:3729:            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
3730:3730:                return new bootstrap.Dropdown(dropdownToggleEl);
3731:3731:            });
3732:3732:
3733:3733:            // Initialize all tooltips
3734:3734:            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
3735:3735:            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
3736:3736:                return new bootstrap.Tooltip(tooltipTriggerEl);
3737:3737:            });
3738:3738:
3739:3739:            // Initialize all popovers
3740:3740:            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
3741:3741:            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
3742:3742:                return new bootstrap.Popover(popoverTriggerEl);
3743:3743:            });
3744:3744:        });
3745:3745:    </script>
3746:3746:    @stack('scripts')
3747:3747:
3748:3748:    <!-- Subscription Modal -->
3749:3749:    <div id="subscriptionModal" class="subscription-modal" style="display: none !important;">
3750:3750:        <!-- Subscription modal content removed -->
3751:3751:    </div>
3752:3752:
3753:3753:    <!-- Add before closing body tag -->
3754:3754:    <script>
3755:3755:        // Pagination functionality
3756:3756:        function setupPagination(tableId, itemsPerPage, totalItems) {
3757:3757:            const pagesCount = Math.ceil(totalItems / itemsPerPage);
3758:3758:            const tableContainer = document.getElementById(tableId);
3759:3759:            
3760:3760:            if (!tableContainer) return;
3761:3761:
3762:3762:            // Create radio buttons for each page
3763:3763:            for (let i = 0; i < pagesCount; i++) {
3764:3764:                const radio = document.createElement('input');
3765:3765:                radio.type = 'radio';
3766:3766:                radio.name = `${tableId}_radio`;
3767:3767:                radio.id = `${tableId}_radio_${i}`;
3768:3768:                radio.className = 'table-radio';
3769:3769:                if (i === 0) radio.checked = true;
3770:3770:                tableContainer.appendChild(radio);
3771:3771:
3772:3772:                // Create display info
3773:3773:                const display = document.createElement('div');
3774:3774:                display.className = 'table-display';
3775:3775:                const start = (i * itemsPerPage) + 1;
3776:3776:                const end = Math.min((i + 1) * itemsPerPage, totalItems);
3777:3777:                display.textContent = `Showing ${start} to ${end} of ${totalItems} items`;
3778:3778:                tableContainer.appendChild(display);
3779:3779:
3780:3780:                // Your table goes here (you'll need to create this dynamically or have multiple tables)
3781:3781:                // Create pagination
3782:3782:                const pagination = document.createElement('div');
3783:3783:                pagination.className = 'pagination';
3784:3784:
3785:3785:                // Previous button
3786:3786:                const prevLabel = document.createElement('label');
3787:3787:                prevLabel.htmlFor = i > 0 ? `${tableId}_radio_${i - 1}` : '';
3788:3788:                prevLabel.className = i === 0 ? 'disabled' : '';
3789:3789:                prevLabel.textContent = '?? Previous';
3790:3790:                pagination.appendChild(prevLabel);
3791:3791:
3792:3792:                // Page numbers
3793:3793:                for (let j = 0; j < pagesCount; j++) {
3794:3794:                    const pageLabel = document.createElement('label');
3795:3795:                    pageLabel.htmlFor = `${tableId}_radio_${j}`;
3796:3796:                    pageLabel.className = i === j ? 'active' : '';
3797:3797:                    pageLabel.textContent = j + 1;
3798:3798:                    pagination.appendChild(pageLabel);
3799:3799:                }
3800:3800:
3801:3801:                // Next button
3802:3802:                const nextLabel = document.createElement('label');
3803:3803:                nextLabel.htmlFor = i < pagesCount - 1 ? `${tableId}_radio_${i + 1}` : '';
3804:3804:                nextLabel.className = i === pagesCount - 1 ? 'disabled' : '';
3805:3805:                nextLabel.textContent = 'Next ??';
3806:3806:                pagination.appendChild(nextLabel);
3807:3807:
3808:3808:                tableContainer.appendChild(pagination);
3809:3809:            }
3810:3810:        }
3811:3811:
3812:3812:        // Initialize pagination for tables
3813:3813:        document.addEventListener('DOMContentLoaded', function() {
3814:3814:            // Example usage:
3815:3815:            // setupPagination('myTable', 20, 95); // 20 items per page, 95 total items
3816:3816:        });
3817:3817:    </script>
3818:3818:
3819:3819:    <!-- Add this at the bottom of your layout file, before the closing </body> tag -->
3820:3820:    
3821:3821:    <script>
3822:3822:        // Empty function that does nothing (keeping for compatibility with any existing calls)
3823:3823:        function logoutToCentralDomain() {
3824:3824:            // This function is deprecated - we now use form submission for proper POST logout
3825:3825:            return false;
3826:3826:        }
3827:3827:    </script>
3828:3828:    
3829:3829:    <!-- Toast container for notifications -->
3830:3830:    <div class="toast-container position-fixed bottom-0 end-0 p-3">
3831:3831:        <div id="themeToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
3832:3832:            <div class="d-flex">
3833:3833:                <div class="toast-body">
3834:3834:                    <i class="fas fa-check-circle me-2"></i>
3835:3835:                    <span id="toastMessage">Theme preference saved</span>
3836:3836:                </div>
3837:3837:                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
3838:3838:            </div>
3839:3839:        </div>
3840:3840:    </div>
3841:3841:    
3842:3842:    <!-- Sidebar compact mode script -->
3843:3843:    <script>
3844:3844:    document.addEventListener('DOMContentLoaded', function() {
3845:3845:        // Check if we should apply compact sidebar mode (for compact layout)
3846:3846:        function checkCompactMode() {
3847:3847:            // If we're in a compact layout page, auto-enable compact sidebar
3848:3848:            if (document.querySelector('.layout-compact')) {
3849:3849:                document.body.classList.add('compact-sidebar');
3850:3850:                console.log('Compact layout detected, enabling compact sidebar');
3851:3851:            }
3852:3852:        }
3853:3853:        
3854:3854:        // Add toggle button to navbar if it doesn't exist
3855:3855:        const navbar = document.querySelector('.top-navbar .container-fluid');
3856:3856:        if (navbar && !document.getElementById('toggleSidebar')) {
3857:3857:            const toggleBtn = document.createElement('button');
3858:3858:            toggleBtn.id = 'toggleSidebar';
3859:3859:            toggleBtn.className = 'btn btn-sm btn-outline-secondary me-2';
3860:3860:            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
3861:3861:            toggleBtn.title = 'Toggle Sidebar';
3862:3862:            
3863:3863:            // Insert at the beginning of navbar
3864:3864:            navbar.insertBefore(toggleBtn, navbar.firstChild);
3865:3865:            
3866:3866:            // Add event listener
3867:3867:            toggleBtn.addEventListener('click', function() {
3868:3868:                document.body.classList.toggle('compact-sidebar');
3869:3869:                
3870:3870:                // Save preference to localStorage
3871:3871:                if (document.body.classList.contains('compact-sidebar')) {
3872:3872:                    localStorage.setItem('sidebarMode', 'compact');
3873:3873:                    showToast('Compact sidebar enabled');
3874:3874:                } else {
3875:3875:                    localStorage.setItem('sidebarMode', 'expanded');
3876:3876:                    showToast('Expanded sidebar enabled');
3877:3877:                }
3878:3878:            });
3879:3879:        }
3880:3880:        
3881:3881:        // Function to show toast notification
3882:3882:        function showToast(message) {
3883:3883:            const toastEl = document.getElementById('themeToast');
3884:3884:            if (toastEl) {
3885:3885:                const toastMessage = document.getElementById('toastMessage');
3886:3886:                if (toastMessage) {
3887:3887:                    toastMessage.textContent = message;
3888:3888:                }
3889:3889:                const bsToast = new bootstrap.Toast(toastEl);
3890:3890:                bsToast.show();
3891:3891:            }
3892:3892:        }
3893:3893:        
3894:3894:        // Apply saved preference from localStorage
3895:3895:        const savedSidebarMode = localStorage.getItem('sidebarMode');
3896:3896:        if (savedSidebarMode === 'compact') {
3897:3897:            document.body.classList.add('compact-sidebar');
3898:3898:        }
3899:3899:        
3900:3900:        // Apply compact mode for compact layout
3901:3901:        checkCompactMode();
3902:3902:        
3903:3903:        // Also check when the window is loaded completely
3904:3904:        window.addEventListener('load', checkCompactMode);
3905:3905:    });
3906:3906:    </script>
3907:3907:
3908:3908:    <!-- Global card style application -->
3909:3909:    <script>
3910:3910:    document.addEventListener('DOMContentLoaded', function() {
3911:3911:        // Apply card style from localStorage on all pages
3912:3912:        const cardStyle = localStorage.getItem('selectedCardStyle') || 'square';
3913:3913:        
3914:3914:        const applyGlobalCardStyle = () => {
3915:3915:            console.log('Applying global card style:', cardStyle);
3916:3916:            
3917:3917:            // Target all card types across all layouts
3918:3918:            const cardSelectors = '.card, .enrolled-card, .stat-card, .compact-content-card, .modern-stat-card, .modern-card';
3919:3919:            
3920:3920:            // Remove all card style classes first
3921:3921:            document.querySelectorAll(cardSelectors).forEach(card => {
3922:3922:                card.classList.remove('card-rounded', 'card-square', 'card-glass');
3923:3923:                // Add the selected style class
3924:3924:                card.classList.add(`card-${cardStyle}`);
3925:3925:            });
3926:3926:            
3927:3927:            // If we're on a dashboard page that has its own applyCardStyle function,
3928:3928:            // it will apply more specific styles later
3929:3929:        };
3930:3930:        
3931:3931:        // Apply global styles
3932:3932:        applyGlobalCardStyle();
3933:3933:        
3934:3934:        // Listen for changes
3935:3935:        window.addEventListener('storage', function(e) {
3936:3936:            if (e.key === 'selectedCardStyle') {
3937:3937:                // Reapply global style
3938:3938:                applyGlobalCardStyle();
3939:3939:            }
3940:3940:        });
3941:3941:    });
3942:3942:    </script>
3943:3943:
3944:3944:    <script>
3945:3945:    document.addEventListener('DOMContentLoaded', function() {
3946:3946:        // Apply card style from localStorage if available
3947:3947:        try {
3948:3948:            const savedCardStyle = localStorage.getItem('selectedCardStyle');
3949:3949:            if (savedCardStyle) {
3950:3950:                applyCardStyle(savedCardStyle);
3951:3951:                console.log('Applied card style from localStorage:', savedCardStyle);
3952:3952:            }
3953:3953:        } catch (e) {
3954:3954:            console.error('Error applying card style from localStorage:', e);
3955:3955:        }
3956:3956:        
3957:3957:        // Listen for card style changes
3958:3958:        document.addEventListener('cardStyleChanged', function(e) {
3959:3959:            const cardStyle = e.detail.cardStyle;
3960:3960:            applyCardStyle(cardStyle);
3961:3961:            console.log('Applied card style from event:', cardStyle);
3962:3962:        });
3963:3963:        
3964:3964:        // Listen for storage events (changes from other tabs)
3965:3965:        window.addEventListener('storage', function(e) {
3966:3966:            if (e.key === 'selectedCardStyle') {
3967:3967:                applyCardStyle(e.newValue);
3968:3968:                console.log('Applied card style from storage event:', e.newValue);
3969:3969:            }
3970:3970:        });
3971:3971:        
3972:3972:        /**
3973:3973:         * Apply card style to all dashboard elements
3974:3974:         */
3975:3975:        function applyCardStyle(style) {
3976:3976:            // Remove all style classes first
3977:3977:            document.body.classList.remove('card-style-square', 'card-style-rounded', 'card-style-glass');
3978:3978:            
3979:3979:            // Add the selected style class
3980:3980:            if (style) {
3981:3981:                document.body.classList.add('card-style-' + style);
3982:3982:            }
3983:3983:        }
3984:3984:    });
3985:3985:    </script>
3986:3986:
3987:3987:    <!-- Include the Tenant Approval Modal -->
3988:3988:    @include('Modals.TenantApproval')
3989:3989:    
3990:3990:    <!-- Scripts to handle tenant approval modal -->
3991:3991:    <script>
3992:3992:        document.addEventListener('DOMContentLoaded', function() {
3993:3993:            // Check if approval modal session flag is set
3994:3994:            @if(session('show_approval_modal'))
3995:3995:                const loginEmail = document.querySelector('input[name="email"]')?.value || '';
3996:3996:                console.log('Email for modal check:', loginEmail);
3997:3997:                
3998:3998:                // Only show approval modal if not a student email
3999:3999:                if (!loginEmail.includes('@student.buksu.edu.ph')) {
4000:4000:                    console.log('Not a student email, showing approval modal');
4001:4001:                    
4002:4002:                    // Check if modal should be prevented (student email was entered)
4003:4003:                    if (sessionStorage.getItem('preventApprovalModal') !== 'true') {
4004:4004:                        // Use Bootstrap 5 Modal API
4005:4005:                        const approvalModal = document.getElementById('tenantApprovalModal');
4006:4006:                        if (approvalModal) {
4007:4007:                            const modal = new bootstrap.Modal(approvalModal);
4008:4008:                            modal.show();
4009:4009:                        }
4010:4010:                    } else {
4011:4011:                        console.log('Modal showing prevented by session storage flag');
4012:4012:                    }
4013:4013:                } else {
4014:4014:                    console.log('Student email detected, not showing approval modal');
4015:4015:                    // Remove the session flag
4016:4016:                    @php
4017:4017:                    if (session()->has('show_approval_modal')) {
4018:4018:                        session()->forget('show_approval_modal');
4019:4019:                    }
4020:4020:                    @endphp
4021:4021:                }
4022:4022:            @endif
4023:4023:            
4024:4024:            // Special check for student emails when page loads
4025:4025:            const emailInput = document.querySelector('input[name="email"]');
4026:4026:            if (emailInput) {
4027:4027:                const checkStudentEmail = function() {
4028:4028:                    const email = emailInput.value || '';
4029:4029:                    if (email.includes('@student.buksu.edu.ph')) {
4030:4030:                        console.log('Student email detected in input');
4031:4031:                        // Hide modal if it's currently shown
4032:4032:                        const modalElement = document.getElementById('tenantApprovalModal');
4033:4033:                        if (modalElement) {
4034:4034:                            const bsModal = bootstrap.Modal.getInstance(modalElement);
4035:4035:                            if (bsModal) {
4036:4036:                                bsModal.hide();
4037:4037:                                console.log('Hiding modal for student email');
4038:4038:                            }
4039:4039:                        }
4040:4040:                    }
4041:4041:                };
4042:4042:                
4043:4043:                // Check on input change
4044:4044:                emailInput.addEventListener('input', checkStudentEmail);
4045:4045:                
4046:4046:                // Check on page load
4047:4047:                checkStudentEmail();
4048:4048:            }
4049:4049:        });
4050:4050:    </script>
4051:4051:
4052:4052:    <!-- Initialize all dropdowns -->
4053:4053:    <script>
4054:4054:        document.addEventListener('DOMContentLoaded', function() {
4055:4055:            // Handle payment method change in sidebar modal
4056:4056:            document.querySelectorAll('#sidebarPremiumModal input[name="payment_method"]').forEach(input => {
4057:4057:                input.addEventListener('change', function() {
4058:4058:                // Hide all payment details
4059:4059:                document.querySelectorAll('#sidebarPremiumModal .payment-details').forEach(el => {
4060:4060:                    el.classList.add('d-none');
4061:4061:                });
4062:4062:                
4063:4063:                // Show selected payment method details
4064:4064:                const method = this.value;
4065:4065:                if (method) {
4066:4066:                    document.getElementById('sidebar_' + method + 'Details')?.classList.remove('d-none');
4067:4067:                }
4068:4068:                });
4069:4069:            });
4070:4070:
4071:4071:            // Initialize all dropdowns
4072:4072:            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
4073:4073:            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
4074:4074:                return new bootstrap.Dropdown(dropdownToggleEl);
4075:4075:            });
4076:4076:        });
4077:4077:    </script>
4078:4078:
4079:4079:    <!-- Sidebar Premium Modal Script -->
4080:4080:    <script>
4081:4081:        document.addEventListener('DOMContentLoaded', function() {
4082:4082:            // Handle payment method change in sidebar modal
4083:4083:            document.querySelectorAll('input[name="payment_method"]').forEach(input => {
4084:4084:                input.addEventListener('change', function() {
4085:4085:                // Hide all payment details
4086:4086:                document.querySelectorAll('#sidebarPremiumModal .payment-details').forEach(el => {
4087:4087:                    el.classList.add('d-none');
4088:4088:                });
4089:4089:                
4090:4090:                // Show selected payment method details
4091:4091:                const method = this.value;
4092:4092:                if (method) {
4093:4093:                    document.getElementById('sidebar_' + method + 'Details')?.classList.remove('d-none');
4094:4094:                }
4095:4095:                });
4096:4096:            });
4097:4097:
4098:4098:            // Form validation and submission
4099:4099:            const sidebarUpgradeForm = document.getElementById('sidebarUpgradeForm');
4100:4100:            if (sidebarUpgradeForm) {
4101:4101:                sidebarUpgradeForm.addEventListener('submit', function(e) {
4102:4102:                    // Get form values
4103:4103:                    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
4104:4104:                    const referenceNumber = document.getElementById('sidebar_reference_number').value;
4105:4105:                    
4106:4106:                    // Basic validation
4107:4107:                    if (!paymentMethod) {
4108:4108:                        e.preventDefault();
4109:4109:                        alert('Please select a payment method');
4110:4110:                        return false;
4111:4111:                    }
4112:4112:                    
4113:4113:                    if (!referenceNumber) {
4114:4114:                        e.preventDefault();
4115:4115:                        alert('Please enter your payment reference number');
4116:4116:                        return false;
4117:4117:                    }
4118:4118:                    
4119:4119:                    // Disable the button and show loading state
4120:4120:                    const submitButton = document.getElementById('sidebarUpgradeButton');
4121:4121:                    if (submitButton) {
4122:4122:                        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
4123:4123:                        submitButton.disabled = true;
4124:4124:                    }
4125:4125:                    
4126:4126:                    // Let the form submit (the controller will handle the upgrade process)
4127:4127:                    return true;
4128:4128:                });
4129:4129:            }
4130:4130:        });
4131:4131:    </script>
4132:4132:
4133:4133:    <!-- Sidebar Premium Modal -->
4134:4134:    @if(!auth()->guard('student')->check())
4135:4135:    <div class="modal fade" id="sidebarPremiumModal" tabindex="-1" aria-labelledby="sidebarPremiumModalLabel" aria-hidden="true">
4136:4136:        <div class="modal-dialog modal-dialog-centered">
4137:4137:            <div class="modal-content border-0 shadow">
4138:4138:                <div class="modal-header bg-gradient-warning text-dark border-0">
4139:4139:                    <h5 class="modal-title" id="sidebarPremiumModalLabel">
4140:4140:                        <i class="fas fa-crown text-warning me-2"></i>Upgrade to Premium
4141:4141:                    </h5>
4142:4142:                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
4143:4143:                </div>
4144:4144:                <div class="modal-body p-4">
4145:4145:                    <!-- Display session messages -->
4146:4146:                    @if(session('success'))
4147:4147:                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
4148:4148:                            <div class="d-flex align-items-center">
4149:4149:                                <i class="fas fa-check-circle me-2"></i>
4150:4150:                            {{ session('success') }}
4151:4151:                            </div>
4152:4152:                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
4153:4153:                        </div>
4154:4154:                    @endif
4155:4155:                    
4156:4156:                    @if(session('error'))
4157:4157:                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
4158:4158:                            <div class="d-flex align-items-center">
4159:4159:                                <i class="fas fa-exclamation-circle me-2"></i>
4160:4160:                            {{ session('error') }}
4161:4161:                            </div>
4162:4162:                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
4163:4163:                        </div>
4164:4164:                    @endif
4165:4165:                    
4166:4166:                    <div class="text-center mb-4">
4167:4167:                        <div class="bg-warning bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
4168:4168:                            <i class="fas fa-crown text-warning fs-1"></i>
4169:4169:                        </div>
4170:4170:                        <h4 class="fw-bold">Unlock Premium Features</h4>
4171:4171:                        <p class="text-muted">Enhance your school management capabilities with our premium plan</p>
4172:4172:                    </div>
4173:4173:                    
4174:4174:                    <div class="card border-0 shadow-sm mb-4">
4175:4175:                        <div class="card-header bg-gradient-light border-0">
4176:4176:                            <div class="d-flex align-items-center">
4177:4177:                                <i class="fas fa-star text-warning me-2"></i>
4178:4178:                                <h5 class="mb-0 fw-semibold">Premium Benefits</h5>
4179:4179:                        </div>
4180:4180:                        </div>
4181:4181:                        <div class="card-body p-0">
4182:4182:                            <ul class="list-group list-group-flush">
4183:4183:                                <li class="list-group-item d-flex align-items-center border-0 py-3">
4184:4184:                                    <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
4185:4185:                                        <i class="fas fa-check text-success"></i>
4186:4186:                                    </div>
4187:4187:                                    <span>Profile customization</span>
4188:4188:                                </li>
4189:4189:                                <li class="list-group-item d-flex align-items-center border-0 py-3">
4190:4190:                                    <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
4191:4191:                                        <i class="fas fa-check text-success"></i>
4192:4192:                                    </div>
4193:4193:                                    <span>Advanced reporting and analytics</span>
4194:4194:                                </li>
4195:4195:                                <li class="list-group-item d-flex align-items-center border-0 py-3">
4196:4196:                                    <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
4197:4197:                                        <i class="fas fa-check text-success"></i>
4198:4198:                                    </div>
4199:4199:                                    <span>Unlimited staff accounts</span>
4200:4200:                                </li>
4201:4201:                                <li class="list-group-item d-flex align-items-center border-0 py-3">
4202:4202:                                    <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
4203:4203:                                        <i class="fas fa-check text-success"></i>
4204:4204:                                    </div>
4205:4205:                                    <span>Priority customer support</span>
4206:4206:                                </li>
4207:4207:                            </ul>
4208:4208:                        </div>
4209:4209:                    </div>
4210:4210:                    
4211:4211:                    <div class="card border-0 shadow-sm mb-4">
4212:4212:                        <div class="card-body p-4">
4213:4213:                            <div class="d-flex justify-content-between align-items-center mb-3">
4214:4214:                                <h6 class="mb-0">Monthly Premium</h6>
4215:4215:                                <span class="badge bg-primary rounded-pill">MOST POPULAR</span>
4216:4216:                            </div>
4217:4217:                            <div class="d-flex justify-content-between align-items-center">
4218:4218:                                <div>
4219:4219:                                    <h3 class="mb-0">???999<span class="small text-muted">/month</span></h3>
4220:4220:                                    <p class="text-muted small mb-0">Auto-renews monthly</p>
4221:4221:                            </div>
4222:4222:                                <div>
4223:4223:                                    <i class="fas fa-rocket text-primary fs-3"></i>
4224:4224:                                </div>
4225:4225:                            </div>
4226:4226:                        </div>
4227:4227:                    </div>
4228:4228:                    
4229:4229:                    <form action="{{ route('tenant.subscription.upgrade', ['tenant' => tenant('id')]) }}" method="POST" id="sidebarUpgradeForm">
4230:4230:                        @csrf
4231:4231:                        <div class="mb-4">
4232:4232:                            <label class="form-label fw-medium">Payment Method</label>
4233:4233:                            <div class="row g-3">
4234:4234:                                <div class="col-4">
4235:4235:                                    <input type="radio" class="btn-check" name="payment_method" id="sidebar_bank_transfer" value="bank_transfer" required>
4236:4236:                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" for="sidebar_bank_transfer">
4237:4237:                                        <i class="fas fa-university fs-3 mb-2"></i>
4238:4238:                                        <span class="small">Bank Transfer</span>
4239:4239:                                    </label>
4240:4240:                                </div>
4241:4241:                                <div class="col-4">
4242:4242:                                    <input type="radio" class="btn-check" name="payment_method" id="sidebar_gcash" value="gcash" required>
4243:4243:                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" for="sidebar_gcash">
4244:4244:                                        <i class="fas fa-wallet fs-3 mb-2"></i>
4245:4245:                                        <span class="small">GCash</span>
4246:4246:                                    </label>
4247:4247:                                </div>
4248:4248:                                <div class="col-4">
4249:4249:                                    <input type="radio" class="btn-check" name="payment_method" id="sidebar_paymaya" value="paymaya" required>
4250:4250:                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" for="sidebar_paymaya">
4251:4251:                                        <i class="fas fa-credit-card fs-3 mb-2"></i>
4252:4252:                                        <span class="small">PayMaya</span>
4253:4253:                                    </label>
4254:4254:                                </div>
4255:4255:                            </div>
4256:4256:                        </div>
4257:4257:                        
4258:4258:                        <div id="sidebar_bank_transferDetails" class="payment-details mb-4 d-none">
4259:4259:                            <div class="card border-0 shadow-sm">
4260:4260:                                <div class="card-header bg-light py-3">
4261:4261:                                    <div class="d-flex align-items-center">
4262:4262:                                        <i class="fas fa-university text-primary me-2"></i>
4263:4263:                                        <h6 class="mb-0">Bank Transfer Instructions</h6>
4264:4264:                                    </div>
4265:4265:                                </div>
4266:4266:                                <div class="card-body">
4267:4267:                                    <p class="mb-3">Please transfer ???999.00 to the following account:</p>
4268:4268:                                    <div class="bg-light p-3 rounded mb-3">
4269:4269:                                        <div class="row g-3">
4270:4270:                                            <div class="col-md-6">
4271:4271:                                                <p class="mb-1 text-muted small">BANK</p>
4272:4272:                                                <p class="mb-0 fw-medium">BDO</p>
4273:4273:                                            </div>
4274:4274:                                            <div class="col-md-6">
4275:4275:                                                <p class="mb-1 text-muted small">ACCOUNT NAME</p>
4276:4276:                                                <p class="mb-0 fw-medium">BukSkwela Inc.</p>
4277:4277:                                            </div>
4278:4278:                                            <div class="col-md-6">
4279:4279:                                                <p class="mb-1 text-muted small">ACCOUNT NUMBER</p>
4280:4280:                                                <p class="mb-0 fw-medium">1234-5678-9012</p>
4281:4281:                                            </div>
4282:4282:                                            <div class="col-md-6">
4283:4283:                                                <p class="mb-1 text-muted small">REFERENCE</p>
4284:4284:                                                <p class="mb-0 fw-medium">Premium-{{ tenant('id') }}</p>
4285:4285:                                            </div>
4286:4286:                                        </div>
4287:4287:                                    </div>
4288:4288:                                    <div class="alert alert-warning d-flex align-items-center small p-2 rounded">
4289:4289:                                        <i class="fas fa-info-circle me-2"></i>
4290:4290:                                        <span>Please include your reference code in the deposit slip/transfer notes</span>
4291:4291:                                    </div>
4292:4292:                                </div>
4293:4293:                            </div>
4294:4294:                        </div>
4295:4295:                        
4296:4296:                        <div id="sidebar_gcashDetails" class="payment-details mb-4 d-none">
4297:4297:                            <div class="card border-0 shadow-sm">
4298:4298:                                <div class="card-header bg-light py-3">
4299:4299:                                    <div class="d-flex align-items-center">
4300:4300:                                        <i class="fas fa-wallet text-primary me-2"></i>
4301:4301:                                        <h6 class="mb-0">GCash Instructions</h6>
4302:4302:                                    </div>
4303:4303:                                </div>
4304:4304:                                <div class="card-body">
4305:4305:                                    <p class="mb-3">Please send ???999.00 to the following GCash account:</p>
4306:4306:                                    <div class="bg-light p-3 rounded mb-3">
4307:4307:                                        <div class="row g-3">
4308:4308:                                            <div class="col-md-6">
4309:4309:                                                <p class="mb-1 text-muted small">GCASH NUMBER</p>
4310:4310:                                                <p class="mb-0 fw-medium">0917-123-4567</p>
4311:4311:                                            </div>
4312:4312:                                            <div class="col-md-6">
4313:4313:                                                <p class="mb-1 text-muted small">ACCOUNT NAME</p>
4314:4314:                                                <p class="mb-0 fw-medium">BukSkwela Inc.</p>
4315:4315:                                            </div>
4316:4316:                                            <div class="col-12">
4317:4317:                                                <p class="mb-1 text-muted small">REFERENCE</p>
4318:4318:                                                <p class="mb-0 fw-medium">Premium-{{ tenant('id') }}</p>
4319:4319:                                            </div>
4320:4320:                                        </div>
4321:4321:                                    </div>
4322:4322:                                    <div class="alert alert-warning d-flex align-items-center small p-2 rounded">
4323:4323:                                        <i class="fas fa-info-circle me-2"></i>
4324:4324:                                        <span>Please include the reference code in the GCash notes</span>
4325:4325:                                    </div>
4326:4326:                                </div>
4327:4327:                            </div>
4328:4328:                        </div>
4329:4329:                        
4330:4330:                        <div id="sidebar_paymayaDetails" class="payment-details mb-4 d-none">
4331:4331:                            <div class="card border-0 shadow-sm">
4332:4332:                                <div class="card-header bg-light py-3">
4333:4333:                                    <div class="d-flex align-items-center">
4334:4334:                                        <i class="fas fa-credit-card text-primary me-2"></i>
4335:4335:                                        <h6 class="mb-0">PayMaya Instructions</h6>
4336:4336:                                    </div>
4337:4337:                                </div>
4338:4338:                                <div class="card-body">
4339:4339:                                    <p class="mb-3">Please send ???999.00 to the following PayMaya account:</p>
4340:4340:                                    <div class="bg-light p-3 rounded mb-3">
4341:4341:                                        <div class="row g-3">
4342:4342:                                            <div class="col-md-6">
4343:4343:                                                <p class="mb-1 text-muted small">PAYMAYA NUMBER</p>
4344:4344:                                                <p class="mb-0 fw-medium">0918-765-4321</p>
4345:4345:                                            </div>
4346:4346:                                            <div class="col-md-6">
4347:4347:                                                <p class="mb-1 text-muted small">ACCOUNT NAME</p>
4348:4348:                                                <p class="mb-0 fw-medium">BukSkwela Inc.</p>
4349:4349:                                            </div>
4350:4350:                                            <div class="col-12">
4351:4351:                                                <p class="mb-1 text-muted small">REFERENCE</p>
4352:4352:                                                <p class="mb-0 fw-medium">Premium-{{ tenant('id') }}</p>
4353:4353:                                            </div>
4354:4354:                                        </div>
4355:4355:                                    </div>
4356:4356:                                    <div class="alert alert-warning d-flex align-items-center small p-2 rounded">
4357:4357:                                        <i class="fas fa-info-circle me-2"></i>
4358:4358:                                        <span>Please include your reference code in the PayMaya notes</span>
4359:4359:                                    </div>
4360:4360:                                </div>
4361:4361:                            </div>
4362:4362:                        </div>
4363:4363:                        
4364:4364:                        <div class="mb-4">
4365:4365:                            <label for="sidebar_reference_number" class="form-label fw-medium">Reference Number</label>
4366:4366:                            <div class="input-group">
4367:4367:                                <span class="input-group-text bg-light"><i class="fas fa-hashtag"></i></span>
4368:4368:                            <input type="text" class="form-control" id="sidebar_reference_number" name="reference_number" placeholder="Enter your payment reference number" required>
4369:4369:                            </div>
4370:4370:                            <div class="form-text">Please enter the reference number from your payment transaction.</div>
4371:4371:                        </div>
4372:4372:                        
4373:4373:                        <div class="d-grid">
4374:4374:                            <button type="submit" class="btn btn-warning btn-lg" id="sidebarUpgradeButton">
4375:4375:                                <i class="fas fa-rocket me-2"></i>Upgrade Now
4376:4376:                            </button>
4377:4377:                        </div>
4378:4378:                    </form>
4379:4379:                </div>
4380:4380:            </div>
4381:4381:        </div>
4382:4382:    </div>
4383:4383:
4384:4384:    <!-- Include the Upgrade Button for non-premium accounts -->
4385:4385:    @if(!$isPremium && !$isUltimate)
4386:4386:        @include('Modals.UpgradeButton')
4387:4387:    @endif
4389:4389:</body>
4390:4390:</html>
