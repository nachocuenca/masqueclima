import Link from "next/link";

export type Breadcrumb = {
  label: string;
  href: string;
};

type BreadcrumbsProps = {
  items: Breadcrumb[];
};

export function Breadcrumbs({ items }: BreadcrumbsProps) {
  return (
    <nav aria-label="Breadcrumb" className="section-shell pt-8 text-sm text-neutral-600">
      <ol className="flex flex-wrap gap-2">
        {items.map((item, index) => (
          <li className="flex items-center gap-2" key={item.href}>
            {index > 0 ? <span aria-hidden="true">/</span> : null}
            {index === items.length - 1 ? (
              <span aria-current="page" className="font-medium text-neutral-900">
                {item.label}
              </span>
            ) : (
              <Link className="focus-ring rounded-sm hover:text-teal-700" href={item.href}>
                {item.label}
              </Link>
            )}
          </li>
        ))}
      </ol>
    </nav>
  );
}
