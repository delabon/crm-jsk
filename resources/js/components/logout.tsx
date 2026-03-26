import {Form, Link} from "@inertiajs/react";
import {logout, logout as logoutRoute} from '@/routes';
import {LogOut} from "lucide-react";


export default function Logout() {
    return <Form {...logoutRoute.form}>
        <Link
            className="block w-full cursor-pointer"
            href={logout()}
            as="button"
            onClick={handleLogout}
            data-test="logout-button"
        >
            <LogOut className="mr-2" />
            Log out
        </Link>
    </Form>
}
