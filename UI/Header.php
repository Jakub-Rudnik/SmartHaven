<?php

namespace UI;

use Interfaces\UIElement;

class Header implements UIElement
{
    private string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function render(): string
    {

        return "
        <div class='d-flex flex-column align-items-start gap-3'>
            <h2 class='font-bold mb-1'><strong>$this->title</strong></h2>
            <div class='d-flex flex-column flex-md-row gap-2 gap-md-4'>
                <div class='d-flex gap-2 align-items-center'>
                    <svg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <g clip-path='url(#clip0_5496_1875)'>
                            <path d='M3.5 0C3.63261 0 3.75979 0.0526784 3.85355 0.146447C3.94732 0.240215 4 0.367392 4 0.5V1H12V0.5C12 0.367392 12.0527 0.240215 12.1464 0.146447C12.2402 0.0526784 12.3674 0 12.5 0C12.6326 0 12.7598 0.0526784 12.8536 0.146447C12.9473 0.240215 13 0.367392 13 0.5V1H14C14.5304 1 15.0391 1.21071 15.4142 1.58579C15.7893 1.96086 16 2.46957 16 3V14C16 14.5304 15.7893 15.0391 15.4142 15.4142C15.0391 15.7893 14.5304 16 14 16H2C1.46957 16 0.960859 15.7893 0.585786 15.4142C0.210714 15.0391 0 14.5304 0 14V5H16V4H0V3C0 2.46957 0.210714 1.96086 0.585786 1.58579C0.960859 1.21071 1.46957 1 2 1H3V0.5C3 0.367392 3.05268 0.240215 3.14645 0.146447C3.24021 0.0526784 3.36739 0 3.5 0V0Z' fill='white'/>
                        </g>
                        <defs>
                            <clipPath id='clip0_5496_1875'>
                                <rect width='16' height='16' fill='white'/>
                            </clipPath>
                        </defs>
                    </svg>
                    <span id='todaysDate'></span>
                </div>
                <div class='d-flex gap-2 align-items-center'>
                    <svg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <g clip-path='url(#clip0_5499_1386)'>
                            <path fill-rule='evenodd' clip-rule='evenodd' d='M16 8C16 10.1217 15.1571 12.1566 13.6569 13.6569C12.1566 15.1571 10.1217 16 8 16C5.87827 16 3.84344 15.1571 2.34315 13.6569C0.842855 12.1566 0 10.1217 0 8C0 5.87827 0.842855 3.84344 2.34315 2.34315C3.84344 0.842855 5.87827 0 8 0C10.1217 0 12.1566 0.842855 13.6569 2.34315C15.1571 3.84344 16 5.87827 16 8ZM8 3.5C8 3.36739 7.94732 3.24021 7.85355 3.14645C7.75979 3.05268 7.63261 3 7.5 3C7.36739 3 7.24021 3.05268 7.14645 3.14645C7.05268 3.24021 7 3.36739 7 3.5V9C7.00003 9.08813 7.02335 9.17469 7.06761 9.25091C7.11186 9.32712 7.17547 9.39029 7.252 9.434L10.752 11.434C10.8669 11.4961 11.0014 11.5108 11.127 11.4749C11.2525 11.4391 11.3591 11.3556 11.4238 11.2422C11.4886 11.1288 11.5065 10.9946 11.4736 10.8683C11.4408 10.7419 11.3598 10.6334 11.248 10.566L8 8.71V3.5Z' fill='white'/>
                        </g>
                        <defs>
                            <clipPath id='clip0_5499_1386'>
                                <rect width='16' height='16' fill='white'/>
                            </clipPath>
                        </defs>
                    </svg>
                    <span id='clock'></span>
                </div>
            </div>
        </div>
        
        <script>
            const date = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('todaysDate').textContent = date.toLocaleDateString('pl-PL', options);
          
            const clock = document.getElementById('clock'); 
            const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' }; 
            clock.textContent = date.toLocaleTimeString('pl-PL', optionsTime);
   
            setInterval(() => {
                    const now = new Date();
                    clock.textContent = now.toLocaleTimeString('pl-PL', optionsTime);
            }, 1000);
        </script>
    ";
    }
}




