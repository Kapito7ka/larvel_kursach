import React from 'react';
import Helmet from 'react-helmet';
import { Link, usePage } from '@inertiajs/react';
import Icon from '@/Components/Icon.jsx';
import Pagination from '@/Components/Pagination.jsx';
import SearchFilter from '@/Components/SearchFilter';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function () {
    const { shows } = usePage().props;
    const { data, links } = shows;

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Shows
                </h2>
            }
        >
            <Helmet title="Shows" />
            <div>
                <h1 className="mb-8 font-bold text-3xl">Shows</h1>
                <div className="mb-6 flex justify-between items-center">
                    <SearchFilter />
                    <Link className="btn-indigo" href={route('shows.create')}>
                        <span>Create</span>
                        <span className="hidden md:inline"> Show</span>
                    </Link>
                </div>
                <div className="bg-white rounded shadow overflow-x-auto">
                    <table className="w-full whitespace-no-wrap">
                        <thead>
                        <tr className="text-left font-bold">
                            <th className="px-6 pt-5 pb-4">Performance</th>
                            <th className="px-6 pt-5 pb-4">Date & Time</th>
                            <th className="px-6 pt-5 pb-4">Price</th>
                            <th className="px-6 pt-5 pb-4">Hall</th>
                            <th className="px-6 pt-5 pb-4"></th>
                        </tr>
                        </thead>
                        <tbody>
                        {data.map(
                            ({ id, performance, datetime, price, hall }) => (
                                <tr
                                    key={id}
                                    className="hover:bg-gray-100 focus-within:bg-gray-100"
                                >
                                    <td className="border-t">
                                        <Link
                                            href={route('shows.edit', id)}
                                            className="px-6 py-4 flex items-center focus:text-indigo-700"
                                        >
                                            {performance.title}
                                        </Link>
                                    </td>
                                    <td className="border-t">
                                        <Link
                                            tabIndex="1"
                                            className="px-6 py-4 flex items-center focus:text-indigo"
                                            href={route('shows.edit', id)}
                                        >
                                            {datetime}
                                        </Link>
                                    </td>
                                    <td className="border-t">
                                        <Link
                                            tabIndex="-1"
                                            href={route('shows.edit', id)}
                                            className="px-6 py-4 flex items-center focus:text-indigo"
                                        >
                                            ${price}
                                        </Link>
                                    </td>
                                    <td className="border-t">
                                        <Link
                                            tabIndex="-1"
                                            href={route('shows.edit', id)}
                                            className="px-6 py-4 flex items-center focus:text-indigo"
                                        >
                                            {hall.name}
                                        </Link>
                                    </td>
                                    <td className="border-t w-px">
                                        <Link
                                            tabIndex="-1"
                                            href={route('shows.edit', id)}
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
                                <td className="border-t px-6 py-4" colSpan="5">
                                    No shows found.
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
};
