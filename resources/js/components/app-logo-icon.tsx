import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 50 L14 22 L32 36 L50 14 L50 50 Z" fill="#F7374F"/>
        </svg>
    );
}
