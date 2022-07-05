<template>
    <div class="profile-view-container">
        <div class="profile-box" v-for="(user, i) in targetedUser" :key="i">
            <perfect-scrollbar v-if="!loadingTargetedUser">
                <div class="profile-avatar-holder">
                    <div class="profile-avatar-box">
                        <img :src="`/storage/locals/${user.profile_picture}`" :alt="`${user.first_name} ${user.surname} profile`">
                        <span class="lock_profile" v-if="user.privacy">
                            <svg viewBox="0 0 382.78 502.66">
                                <path fill="#4563F7" d="M241.88,507.66c-9.33-1.47-18.8-2.36-28-4.51C149.32,488.05,106,447.89,80.09,388,69,362.3,64,335.21,64.24,307.24c.41-51.53,42.81-94.56,94.33-94.89q93.69-.6,187.39,0c55.25.39,100.44,46.14,101.05,101.33,1.05,94.77-68.63,176.93-162.65,191.79-5.31.83-10.65,1.46-16,2.19ZM270.35,357c25-10.44,32.65-33.45,28.25-51.94a44.19,44.19,0,0,0-85.86-.39c-4.49,17.83,2.41,41.63,28.16,52.33v5.24q0,35.33,0,70.65c0,5.85,2.1,10.57,7.11,13.66a13.67,13.67,0,0,0,14.46.43c5.38-2.92,7.89-7.61,7.89-13.67C270.35,408,270.35,382.63,270.35,357Z" transform="translate(-64.24 -5)"/>
                                <path fill="#4563F7" d="M388.13,191c-19.22-7.11-38.59-9.06-58.89-8,0-2.41,0-4.31,0-6.21-.28-16.66.67-33.46-1.12-50-3.86-35.42-36-62.65-72.18-62.75-36.52-.09-68.33,27.09-73,62.61a161.6,161.6,0,0,0-.91,19.07c-.14,12.09,0,24.19,0,37.11-19.79-.26-39.54-.73-59.15,6.73,1.63-31-3-61.2,6-90.73C146.56,40.64,203.31,1.1,263.71,5.31,326,9.64,376.64,55.34,386.23,116a156.59,156.59,0,0,1,1.82,22C388.31,155.42,388.13,172.86,388.13,191Z" transform="translate(-64.24 -5)"/>
                                <path fill="#4563F7" d="M255.75,330a14.7,14.7,0,1,1,14.58-14.7A14.86,14.86,0,0,1,255.75,330Z" transform="translate(-64.24 -5)"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="profile-names-holder">
                    <h2 class="profile-name">
                        {{user.first_name}} {{user.surname}}
                        <span class="name-age">{{user.birthdate}} years old</span>
                    </h2>
                </div>

                <div class="profile-controls-holder">
                    <div class="profile-controls-box">
                        <form action="/" method="post" enctype="multipart/form-data" @submit.prevent="sendContactRequest" v-if="userAddable">
                            <button type="submit" role="button">
                                <svg viewBox="0 0 485.83 504.89">
                                    <path d="M160.62,267.53C121,236.86,99.45,197.71,99.71,147.85c.21-40.32,15.49-74.54,44.17-102.64,56.36-55.22,147-54.67,202.71,1.46,30.9,31.11,45.4,68.91,42.32,112.87s-23.6,78.75-58,106c9.62,4.4,19,8.54,28.21,12.94a124.08,124.08,0,0,1,12.11,6.8c12.05,7.52,15.15,20,8,31.73-6.95,11.42-19.6,14.76-31.45,7.65-18.32-11-37.69-19.27-58.56-23.83C187.48,278.66,87.69,341.51,64.52,441.78c-3.3,14.28-4.25,29.19-5.25,43.88-.94,13.86-9.72,23.45-23.19,23.41s-22.83-9.65-22.91-23.54c-.47-92.95,56.58-179.28,142.72-215.95Zm83.84-19.37a99,99,0,1,0-98.88-98.57A98.66,98.66,0,0,0,244.46,248.16Z" transform="translate(-13.17 -4.18)"/>
                                    <path d="M430.49,438.54c0,15.32.08,29.91,0,44.51-.12,16.29-12.81,26.36-28.67,22.89-10.28-2.24-17.11-10.64-17.21-21.54-.12-13.37,0-26.74,0-40.11,0-1.74,0-3.48,0-5.75-15.78,0-31.06,0-46.35,0-14.57,0-24.1-10.15-23.17-24.57.8-12.33,10.45-21.16,23.74-21.31,15-.16,29.94,0,45.78,0,0-2.06,0-3.93,0-5.8,0-13.19-.06-26.39,0-39.58s9.93-23.16,22.83-23.18,22.91,9.88,23.05,23.06c.15,14.94,0,29.88,0,45.5h8.76c12.13,0,24.25-.1,36.38,0,13.91.15,23.4,9.61,23.38,23.08s-9.45,22.77-23.48,22.81C460.75,438.57,446,438.54,430.49,438.54Z" transform="translate(-13.17 -4.18)"/>
                                </svg>
                                <span>
                                    Add Contact
                                </span>
                            </button>
                        </form>
                        <form action="/" method="post" enctype="multipart/form-data" @submit.prevent="acceptContactRequest" v-if="requestAcceptable">
                            <button type="submit" role="button">                                                                        
                                <svg viewBox="0 0 452.1 459.41">
                                    <path d="M324.81,263.58c12.24,6.21,24.61,12.14,36.63,18.7a18.15,18.15,0,0,1,9.88,18.16c-.7,8.71-5.11,15.31-13.18,18.93-6.76,3-13.24,1.58-19.39-2.17-17.55-10.69-36.32-18.37-56.57-22-53.63-9.67-101.87,2.41-143.81,37.23-32.5,27-51.75,62-58.74,103.6-1.52,9-1.85,18.33-2.18,27.53C77,476.24,68.85,484.89,56.62,485c-12.41.05-20.91-8.76-20.94-21.71-.2-84.17,51.2-162.09,129-195.5,1.43-.61,2.84-1.26,4.8-2.14C130.29,235.32,111,196.07,114.63,147c2.54-34.15,17.39-63.05,42.78-86,54-48.84,134.75-45.13,184.64,7.89C391.68,121.55,391.44,212.66,324.81,263.58Zm-168.92-105a89.92,89.92,0,0,0,179.83-.34,89.92,89.92,0,1,0-179.83.34Z" transform="translate(-35.68 -26.55)"/>
                                    <path d="M349.78,429.36a17.81,17.81,0,0,1,1.8-2.59q49.91-50,99.85-99.9c6.12-6.11,13.74-7.45,20.48-3.43a30.74,30.74,0,0,1,5,4.18c2.1,2,4.12,4,6.1,6.09a16.17,16.17,0,0,1,.21,22.65c-.57.63-1.17,1.22-1.77,1.82l-123,123c-6.35,6.35-12.05,6.35-18.38,0l-58.26-58.28c-8.25-8.25-8.25-17.46,0-25.72,2-2,3.93-4,5.92-5.92,7.06-6.92,16.7-7,23.73,0,11.95,11.83,23.81,23.77,35.71,35.65C347.86,427.56,348.54,428.18,349.78,429.36Z" transform="translate(-35.68 -26.55)"/>
                                </svg>
                                <span>
                                    Approve Request
                                </span>
                            </button>
                        </form>
                        <form action="/" method="post" enctype="multipart/form-data" @submit.prevent="cancelContactRequest" v-if="requestCancellable">
                            <button type="submit" role="button">                                                                        
                                <svg viewBox="0 0 452.1 459.41">
                                    <path d="M324.81,263.58c12.24,6.21,24.61,12.14,36.63,18.7a18.15,18.15,0,0,1,9.88,18.16c-.7,8.71-5.11,15.31-13.18,18.93-6.76,3-13.24,1.58-19.39-2.17-17.55-10.69-36.32-18.37-56.57-22-53.63-9.67-101.87,2.41-143.81,37.23-32.5,27-51.75,62-58.74,103.6-1.52,9-1.85,18.33-2.18,27.53C77,476.24,68.85,484.89,56.62,485c-12.41.05-20.91-8.76-20.94-21.71-.2-84.17,51.2-162.09,129-195.5,1.43-.61,2.84-1.26,4.8-2.14C130.29,235.32,111,196.07,114.63,147c2.54-34.15,17.39-63.05,42.78-86,54-48.84,134.75-45.13,184.64,7.89C391.68,121.55,391.44,212.66,324.81,263.58Zm-168.92-105a89.92,89.92,0,0,0,179.83-.34,89.92,89.92,0,1,0-179.83.34Z" transform="translate(-35.68 -26.55)"/>
                                    <path d="M349.78,429.36a17.81,17.81,0,0,1,1.8-2.59q49.91-50,99.85-99.9c6.12-6.11,13.74-7.45,20.48-3.43a30.74,30.74,0,0,1,5,4.18c2.1,2,4.12,4,6.1,6.09a16.17,16.17,0,0,1,.21,22.65c-.57.63-1.17,1.22-1.77,1.82l-123,123c-6.35,6.35-12.05,6.35-18.38,0l-58.26-58.28c-8.25-8.25-8.25-17.46,0-25.72,2-2,3.93-4,5.92-5.92,7.06-6.92,16.7-7,23.73,0,11.95,11.83,23.81,23.77,35.71,35.65C347.86,427.56,348.54,428.18,349.78,429.36Z" transform="translate(-35.68 -26.55)"/>
                                </svg>
                                <span>
                                    Cancel Request
                                </span>
                            </button>
                        </form>
                        <form action="/" method="post" enctype="multipart/form-data" @submit.prevent="unBlockContact" v-if="!contactBlockable">
                            <button type="submit" role="button">
                                <svg viewBox="0 0 499.22 499.43">
                                    <path d="M238.92,6.43h33.15C278,7.16,284,7.78,289.86,8.65c48.21,7.09,91.64,25.48,127.89,58.1,75.16,67.64,102.31,151.66,79.56,250.19C483.4,377.16,449.13,425,398.49,460.39c-58.87,41.13-124.2,54.15-194.42,39.9-52.78-10.71-97.33-36.9-133.53-76.8C38.4,388.05,17.93,346.69,9.76,299.43c-1.54-8.9-2.59-17.88-3.87-26.82V239.46c.31-1.4.72-2.78.91-4.19,1.12-8.51,1.68-17.13,3.35-25.52Q28.53,117,103,58.58A240,240,0,0,1,212.06,10.31C221,8.79,230,7.71,238.92,6.43ZM112.53,416.57C174,467.08,243.18,485,319.1,460.51,405.64,432.6,456.26,371,468.38,280.82c8.49-63.12-11-119.21-52.61-167.49Zm-24.87-27L389,88.2c-86-68.09-208.47-61.32-285.5,16.23C26.23,182.24,19.65,303.86,87.66,389.56Z" transform="translate(-5.89 -6.43)"/>
                                </svg>
                                <span>
                                    Unblock
                                </span>
                            </button>
                        </form>
                        <a :href="`/messages/${user.username}`" target="_self" v-if="userMessageable">
                            <svg viewBox="0 0 476.71 460.99">
                                <path d="M17.65,241.53c1-3.92,1.84-7.86,2.89-11.76A83.59,83.59,0,0,1,100.73,168q84.63-.19,169.28,0c43.59.13,80.93,35,82.25,78.56,1.15,38.07,1.48,76.26-.27,114.28-1.59,34.6-28,64.59-63.38,72.9A97.43,97.43,0,0,1,266.77,436c-51.52.19-103,.05-154.56.23a16.09,16.09,0,0,0-8.84,3c-18.31,13.44-36.43,27.14-54.6,40.78-13.64,10.25-22.46,8.64-31.12-5.71ZM51.31,435.89c13-9.75,25-18.54,36.73-27.62a26.52,26.52,0,0,1,17.15-5.82q81.72.27,163.44.07a50,50,0,0,0,50.44-50.45q.14-50.07-.05-100.13a50.73,50.73,0,0,0-1.49-12.09c-5.9-23.65-25.44-38.2-51.53-38.2q-77.06,0-154.1.33c-8,0-16.2,0-23.82,2-22.73,5.82-36.72,25.52-36.75,50.6q-.11,87.85,0,175.71Z" transform="translate(-17.65 -25.51)"/>
                                <path d="M460.64,293.09v-5.95q0-87.12,0-174.23c0-16.38-4.95-30.51-17.54-41.43-8.95-7.77-19.44-12.08-31.31-12.1q-85.15-.16-170.31,0c-26.17.08-48.33,22.45-48.62,48.63-.14,11.78,0,23.56,0,35.34,0,10.45-7.43,18.22-17.16,18.06s-16.66-7.78-16.58-18.13c.09-13.41-.13-26.84.45-40.23,1.58-36.44,31.29-69.84,67.37-75.93a110.18,110.18,0,0,1,18-1.52q82-.18,163.93-.06c41.72,0,74.83,26.57,83.72,67.22a73.75,73.75,0,0,1,1.6,15.55q.15,108.47.12,216.94c0,7.43-2.64,13.1-9.14,16.6-6.8,3.66-13.23,2.29-19.23-2.22-19.34-14.53-38.65-29.12-58.13-43.48a15.18,15.18,0,0,0-8-2.65c-7.84-.33-15.7-.07-23.55-.05-10.23,0-17.93-7.05-18.12-16.66s7.66-17.07,18.16-17.08,21.27.12,31.9,0a21.8,21.8,0,0,1,14.06,4.71C434.67,273.75,447.13,283,460.64,293.09Z" transform="translate(-17.65 -25.51)"/>
                                <path d="M168.15,285.77q-25.28,0-50.53,0c-10.39,0-17.88-6.95-18-16.6s7.55-16.89,18.18-16.9q51.27,0,102.54,0c8.67,0,15.13,5.79,16.49,14.4a16.47,16.47,0,0,1-11.4,18.3,23,23,0,0,1-6.75.76C201.84,285.8,185,285.77,168.15,285.77Z" transform="translate(-17.65 -25.51)"/>
                                <path d="M143.43,319.12c8.82,0,17.65-.05,26.48,0a16.79,16.79,0,1,1,.49,33.58q-27.21.24-54.43,0a16.58,16.58,0,0,1-16.34-16.74,16.81,16.81,0,0,1,16.83-16.81c4.58-.05,9.15,0,13.73,0Z" transform="translate(-17.65 -25.51)"/>
                            </svg>
                            <span>
                                Message
                            </span>
                        </a>
                        <div class="more-options" @click.stop="toggleMoreOptions">
                            <svg viewBox="0 0 502.74 110.01">
                                <path d="M507.33,264.41c-2.48,9.12-5.15,18.17-11.37,25.64-15.24,18.3-34.57,25.6-57.65,19.37-22.56-6.09-36.16-21.42-40-44.61-4.8-28.83,15.1-57.14,43.27-62.18,30.63-5.47,59,13.55,64.8,43.37a19.31,19.31,0,0,0,1,2.7Z" transform="translate(-4.59 -201.59)"/>
                                <path d="M4.59,256.49a55,55,0,1,1,109.91.37c-.12,30.13-25,54.7-55.21,54.65A54.86,54.86,0,0,1,4.59,256.49Z" transform="translate(-4.59 -201.59)"/>
                                <path d="M252.65,311.51C222.18,311.45,198,286.79,198,256c.08-30.13,24.88-54.5,55.33-54.36,30.05.15,54.72,25.15,54.56,55.32S282.89,311.57,252.65,311.51Z" transform="translate(-4.59 -201.59)"/>
                            </svg>
                            <div class="more-options-box" :class="[profileMoreOptions ? 'more-options-box__active' : 'more-options-box__inactive']">
                                <form action="/" method="post" enctype="multipart/form-data" v-if="contactRemovable" @submit.prevent="removeContact">
                                    <button type="submit" role="button">
                                        <svg viewBox="0 0 482.6 501.55">
                                            <path d="M331,264.57c13.4,6.79,26.93,13.28,40.09,20.46,7.52,4.11,11.5,11.13,10.8,19.88-.76,9.52-5.58,16.74-14.42,20.7-7.39,3.31-14.48,1.73-21.22-2.37-19.2-11.7-39.73-20.1-61.89-24.1C225.7,288.55,172.92,301.77,127,339.86c-35.56,29.53-56.62,67.85-64.27,113.36-1.67,9.9-2,20.06-2.39,30.12-.48,13.9-9.41,23.38-22.79,23.44C24,506.84,14.7,497.19,14.67,483c-.23-92.08,56-177.34,141.11-213.9,1.57-.67,3.11-1.38,5.26-2.34-42.86-33.12-64-76.08-60-129.8,2.77-37.38,19-69,46.8-94.15,59-53.43,147.44-49.37,202,8.64C404.18,109.17,403.92,208.86,331,264.57ZM146.19,149.67a98.38,98.38,0,1,0,98.09-98.84A97.57,97.57,0,0,0,146.19,149.67Z" transform="translate(-14.67 -5.22)"/>
                                            <rect x="383.57" y="322.8" width="45.69" height="181.7" rx="22.85" transform="translate(805.4 2.01) rotate(90)"/>
                                        </svg>
                                        <span>
                                            Remove Contact
                                        </span>
                                    </button>
                                </form>
                                <form action="/" method="post" @submit.prevent="rejectContactRequest" v-if="requestRejectable" enctype="multipart/form-data">
                                    <button type="submit" role="button">                                 
                                        <svg viewBox="0 0 465.73 504.89">
                                            <path d="M160.62,267.53C121,236.86,99.45,197.71,99.71,147.85c.21-40.32,15.49-74.54,44.17-102.64,56.36-55.22,147-54.67,202.71,1.46,30.9,31.11,45.4,68.91,42.32,112.87s-23.6,78.75-58,106c9.62,4.4,19,8.54,28.21,12.94a124.08,124.08,0,0,1,12.11,6.8c12.05,7.52,15.15,20,8,31.73-6.95,11.42-19.6,14.76-31.45,7.65-18.32-11-37.69-19.27-58.56-23.83C187.48,278.66,87.69,341.51,64.52,441.78c-3.3,14.28-4.25,29.19-5.25,43.88-.94,13.86-9.72,23.45-23.19,23.41s-22.83-9.65-22.91-23.54c-.47-92.95,56.58-179.28,142.72-215.95Zm83.84-19.37a99,99,0,1,0-98.88-98.57A98.66,98.66,0,0,0,244.46,248.16Z" transform="translate(-13.17 -4.18)"/>
                                            <path d="M407.2,448.36c-10.83,10.83-21.1,21.21-31.49,31.46-11.6,11.44-27.7,9.58-36.46-4.08-5.68-8.86-4.57-19.63,3.07-27.4,9.37-9.54,18.88-18.94,28.33-28.39,1.24-1.23,2.47-2.47,4.07-4.07-11.15-11.16-22-21.95-32.76-32.77-10.29-10.32-9.87-24.23,1-33.76,9.28-8.16,22.36-7.57,31.86,1.72,10.7,10.46,21.2,21.14,32.39,32.34l4.11-4.11c9.33-9.33,18.62-18.69,28-28s23.4-9.36,32.53-.25,9.22,23.18,0,32.6c-10.45,10.67-21.1,21.15-32.15,32.2l6.2,6.19c8.57,8.58,17.21,17.08,25.7,25.75,9.73,9.94,9.75,23.34.21,32.85s-22.78,9.42-32.73-.47C428.58,469.78,418.15,459.31,407.2,448.36Z" transform="translate(-13.17 -4.18)"/>
                                        </svg>
                                        <span>
                                            Reject Request
                                        </span>
                                    </button>
                                </form>
                                <form action="/" method="post" enctype="multipart/form-data" @submit.prevent="markContactAsSpam" v-if="markContactSpam">
                                    <button type="submit" role="button">
                                        <svg viewBox="0 0 500.44 457.76">
                                            <path d="M5.78,388c2.48-22.77,14.55-41.46,25.69-60.56,9.43-16.17,18.75-32.41,28.11-48.62q59-102.12,117.94-204.25c14.31-24.85,34.94-40.49,63.36-46,32.21-6.29,70.19,8.45,87.47,36.32,18.51,29.87,35.72,60.55,53.33,91Q437.33,252,492.82,348.26c12,20.72,16.41,42.57,11.32,66.2-8.88,41.2-45.2,70.2-88.78,70.34-47.25.15-94.51,0-141.76,0q-88.24,0-176.48,0c-28.5,0-52.4-10.3-70.75-32.42a89.28,89.28,0,0,1-19.73-45c-.18-1.26-.57-2.48-.86-3.72ZM255.2,444.47q79.17,0,158.34,0c15,0,28.24-4.25,38.7-15.44,15.64-16.74,17.91-39.28,6-60q-63.18-109.51-126.45-219C320,129.67,308.61,109,296,89c-10.16-16.09-25.9-22.79-44.83-21-17.63,1.63-29.87,11.31-38.58,26.44q-62.25,108.09-124.75,216c-12.28,21.27-25,42.32-36.51,64-8.6,16.23-6.87,32.85,3.15,48.22,10.14,15.55,25.18,21.82,43.33,21.81Q176.52,444.43,255.2,444.47Z" transform="translate(-5.78 -27.12)"/>
                                            <path d="M281.54,198.57q-3,51.42-6,102.84c-.27,4.7-.06,9.48-.83,14.09a18.78,18.78,0,0,1-19,15.7c-9.3-.14-17.26-6.31-18.33-15.69-1.45-12.76-1.9-25.62-2.7-38.45-1.63-26.14-3.09-52.3-4.83-78.44-1.45-21.9,18.07-37.36,36.73-28.69C278.74,175.56,282.32,186,281.54,198.57Z" transform="translate(-5.78 -27.12)"/>
                                            <path d="M255.52,400.1a25.61,25.61,0,0,1,1.1-51.21c14.14.27,25.06,12.11,24.6,26.67A25.29,25.29,0,0,1,255.52,400.1Z" transform="translate(-5.78 -27.12)"/>
                                        </svg>
                                        <span>
                                            Mark as Spam
                                        </span>
                                    </button>
                                </form>
                                <form action="/" method="post" enctype="multipart/form-data" @submit.prevent="blockContact" v-if="contactBlockable">
                                    <button type="submit" role="button">
                                        <svg viewBox="0 0 499.22 499.43">
                                            <path d="M238.92,6.43h33.15C278,7.16,284,7.78,289.86,8.65c48.21,7.09,91.64,25.48,127.89,58.1,75.16,67.64,102.31,151.66,79.56,250.19C483.4,377.16,449.13,425,398.49,460.39c-58.87,41.13-124.2,54.15-194.42,39.9-52.78-10.71-97.33-36.9-133.53-76.8C38.4,388.05,17.93,346.69,9.76,299.43c-1.54-8.9-2.59-17.88-3.87-26.82V239.46c.31-1.4.72-2.78.91-4.19,1.12-8.51,1.68-17.13,3.35-25.52Q28.53,117,103,58.58A240,240,0,0,1,212.06,10.31C221,8.79,230,7.71,238.92,6.43ZM112.53,416.57C174,467.08,243.18,485,319.1,460.51,405.64,432.6,456.26,371,468.38,280.82c8.49-63.12-11-119.21-52.61-167.49Zm-24.87-27L389,88.2c-86-68.09-208.47-61.32-285.5,16.23C26.23,182.24,19.65,303.86,87.66,389.56Z" transform="translate(-5.89 -6.43)"/>
                                        </svg>
                                        <span>
                                            Block
                                        </span>
                                    </button>
                                </form>
                                <form action="/" method="post" enctype="multipart/form-data" @submit.prevent="unMarkAsSpam" v-if="unMarkContactSpam">
                                    <button type="submit" role="button">
                                        <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 499.22 499.43">
                                            <path d="M238.92,6.43h33.15C278,7.16,284,7.78,289.86,8.65c48.21,7.09,91.64,25.48,127.89,58.1,75.16,67.64,102.31,151.66,79.56,250.19C483.4,377.16,449.13,425,398.49,460.39c-58.87,41.13-124.2,54.15-194.42,39.9-52.78-10.71-97.33-36.9-133.53-76.8C38.4,388.05,17.93,346.69,9.76,299.43c-1.54-8.9-2.59-17.88-3.87-26.82V239.46c.31-1.4.72-2.78.91-4.19,1.12-8.51,1.68-17.13,3.35-25.52Q28.53,117,103,58.58A240,240,0,0,1,212.06,10.31C221,8.79,230,7.71,238.92,6.43ZM112.53,416.57C174,467.08,243.18,485,319.1,460.51,405.64,432.6,456.26,371,468.38,280.82c8.49-63.12-11-119.21-52.61-167.49Zm-24.87-27L389,88.2c-86-68.09-208.47-61.32-285.5,16.23C26.23,182.24,19.65,303.86,87.66,389.56Z" transform="translate(-5.89 -6.43)"/>
                                        </svg>
                                        <span>
                                            Spam Unmark
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-desc-holder" v-if="user.bio">
                    <markdown class="md-body" :content="user.bio"></markdown>
                </div>
            </perfect-scrollbar>
            <div class="profileView-close">
                <button class="btnClose" @click="closeProfileView">Close</button>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    import Markdown from 'markdown-it-vue/dist/markdown-it-vue-light.umd.min.js'
    import 'markdown-it-vue/dist/markdown-it-vue-light.css'

    export default {
        name: 'ProfileView',
        props: ['userId'],
        data () {
            return {
            }
        },
        components: {
            Markdown,
        },
        computed: {
            ...mapGetters([
                'contacts',
                'targetedUser',
                'userAddable',
                'userMessageable',
                'requestRejectable',
                'requestCancellable',
                'requestAcceptable',
                'contactBlockable',
                'contactRemovable',
                'markContactSpam',
                'unMarkContactSpam',
                'loadingTargetedUser',
                'userTarget',
                'pendingResponse',
                'pendingRequests',
                'profileMoreOptions',
            ]),
        },
        methods: {
            async getUser () {
                await this.$store.dispatch('collectUserInfo')
            },
            closeProfileView () {
                this.$store.dispatch('toggleProfileView', false)
                this.$store.dispatch('setUserTarget', '')
                this.$store.dispatch('unloadTargetUser')
            },
            sendContactRequest ( ){
                if (this.userAddable) {
                    this.$store.dispatch('sendContactRequest', this.userTarget)
                }
            },
            cancelContactRequest () {
                if (this.requestCancellable) {
                    this.$store.dispatch('cancelContactRequest', this.userTarget)
                }
            },
            rejectContactRequest (){
                if (this.requestRejectable) {
                    this.$store.dispatch('rejectContactRequest', this.userTarget)
                    this.$store.dispatch('markPendingRequests', this.userTarget)
                }
            },
            acceptContactRequest () {
                if (this.requestAcceptable){ 
                    this.$store.dispatch('acceptContactRequest', this.userTarget)
                    this.$store.dispatch('markPendingRequests', this.userTarget)
                }
            },
            removeContact () {
                this.$store.dispatch('removeContact', this.userTarget)
            },
            blockContact () {
                if (this.contactBlockable) {
                    this.$store.dispatch('blockContact', this.userTarget)
                }
            },
            unBlockContact () {
                if (!this.contactBlockable) {
                    this.$store.dispatch('unBlockContact', this.userTarget)
                }
            },
            markContactAsSpam () {
                if (this.markContactSpam) {
                    this.$store.dispatch('markAsSpam', this.userTarget)
                }
            },
            unMarkAsSpam ( ){
                if (this.unMarkContactSpam) {
                    this.$store.dispatch('unMarkAsSpam', this.userTarget)
                }
            },
            toggleMoreOptions () {
                this.$store.dispatch('profileMoreOptions')
            },
            async contactRemovalListener () {
                await Echo.private(`contactRemoval.${this.userId}`)
                    .listen('ContactRemoveEvent', (response) => {
                        this.$store.dispatch('userAddableAction', response.interacts.add)
                        this.$store.dispatch('userMessageableAction', response.interacts.message)
                        this.$store.dispatch('contactRemovableAction', response.interacts.remove)
                    })
            },
            async contactBlockedListener () {
                await Echo.private(`contactBlocked.${this.userId}`)
                    .listen('ContactBlockEvent', (response) => {
                        this.$store.dispatch('userAddableAction', response.interacts.add)
                        this.$store.dispatch('userMessageableAction', response.interacts.message)
                        this.$store.dispatch('requestAcceptableAction', response.interacts.accept)
                        this.$store.dispatch('requestCancellableAction', response.interacts.cancel)
                        this.$store.dispatch('requestRejectableAction', response.interacts.reject)
                        this.$store.dispatch('markContactSpamAction', response.interacts.spam)
                    })
            },
            async contactSpamMarkListener () {
                await Echo.private(`contactSpamMark.${this.userId}`)
                    .listen('ContactMarkAsSpamEvent', (response) => {
                        this.$store.dispatch('userAddableAction', response.interacts.add)
                        this.$store.dispatch('requestCancellableAction', response.interacts.cancel)
                    })
            },
            async contactSpamUnMarkListener () {
                await Echo.private(`contactUnmarkSpam.${this.userId}`)
                    .listen('ContactSpamUnmarkEvent', (response) => {
                        this.$store.dispatch('userAddableAction', response.interacts.add)
                        this.$store.dispatch('markContactSpamAction', response.interacts.spam)
                    })
            }
        },
        mounted () {
            this.getUser()
            this.contactRemovalListener()
            this.contactBlockedListener()
            this.contactSpamMarkListener()
            this.contactSpamUnMarkListener()

            const parentHeight = document.querySelector('.profile-view-container').offsetHeight / 1.5
            if (document.querySelector('.profile-box')) {
                document.querySelector('.profile-box').style.maxHeight = `${parentHeight}px`
            }

            this.$store.state.generals.rightActivePanels = false
            this.$store.state.generals.leftActivePanels = false
            this.$store.state.generals.searchShow = false
            this.$store.state.generals.preferencesShow = false
            this.$store.state.generals.profilePreview = false
        }
    }
</script>
