import React from 'react';
import Helmet from 'react-helmet';
import { Link, usePage } from '@inertiajs/react';
import Icon from '@/Components/Icon.jsx';
import Pagination from '@/Components/Pagination.jsx';
import SearchFilter from '@/Components/SearchFilter';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function () {
    const { producers } = usePage().props;
    const { data, links } = producers;

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Producers
                </h2>
            }
        >
            <Helmet title="Producers" />
            <div>
                <h1 className="mb-8 font-bold text-3xl">Producers</h1>
                <div className="mb-6 flex justify-between items-center">
                    <SearchFilter />
                    <Link className="btn-indigo" href={route('producers.create')}>
                        <span>Create</span>
                        <span className="hidden md:inline"> Producer</span>
                    </Link>
                </div>
                <div className="bg-white rounded shadow overflow-x-auto">
                    <table className="w-full whitespace-no-wrap">
                        <thead>
                        <tr className="text-left font-bold">
                            <th className="px-6 pt-5 pb-4">Name</th>
                            <th className="px-6 pt-5 pb-4">Phone</th>
                            <th className="px-6 pt-5 pb-4">Email</th>
                            <th className="px-6 pt-5 pb-4"></th>
                        </tr>
                        </thead>
                        <tbody>
                        {data.map(
                            ({ id, full_name, phone_number, email, deleted_at }) => (
                                <tr
                                    key={id}
                                    className="hover:bg-gray-100 focus-within:bg-gray-100"
                                >
                                    <td className="border-t">
                                        <Link
                                            href={route('producers.edit', id)}
                                            className="px-6 py-4 flex items-center focus:text-indigo-700"
                                        >
                                            {full_name}
                                            {deleted_at && (
                                                <Icon
                                                    name="trash"
                                                    className="flex-shrink-0 w-3 h-3 text-gray-400 fill-current ml-2"
                                                />
                                            )}
                                        </Link>
                                    </td>
                                    <td className="border-t">
                                        <Link
                                            tabIndex="1"
                                            className="px-6 py-4 flex items-center focus:text-indigo"
                                            href={route('producers.edit', id)}
                                        >
                                            {phone_number}
                                        </Link>
                                    </td>
                                    <td className="border-t">
                                        <Link
                                            tabIndex="-1"
                                            href={route('producers.edit', id)}
                                            className="px-6 py-4 flex items-center focus:text-indigo"
                                        >
                                            {email}
                                        </Link>
                                    </td>
                                    <td className="border-t w-px">
                                        <Link
                                            tabIndex="-1"
                                            href={route('producers.edit', id)}
                                            className="px-4 flex items-center"
                                        >
                                            <Icon
                                                name="cheveron-right"
                                                className="block w-6 h-6 text-gray-400 fill-current"
                                            />
                                        </Link>
                                    </td>
                                </tr>
                            )
                        )}
                        {data.length === 0 && (
                            <tr>
                                <td className="border-t px-6 py-4" colSpan="4">
                                    No producers found.
                                </td>
                            </tr>
                        )}
                        </tbody>
                    </table>
                </div>
                <Pagination links={links} />
            </div>
        </AuthenticatedLayout>
    );
}
