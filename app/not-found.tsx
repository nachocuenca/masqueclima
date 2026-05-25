import Link from "next/link";
import { Footer } from "@/components/layout/Footer";
import { Header } from "@/components/layout/Header";
import es from "@/content/i18n/es";

export default function NotFound() {
  return (
    <>
      <Header dictionary={es} lang="es" />
      <main className="section-shell py-24">
        <h1 className="text-4xl font-black text-neutral-950">404</h1>
        <p className="mt-4 max-w-2xl text-lg leading-8 text-neutral-700">
          {es["404"].message.replace("{site}", "+QUECLIMA")}
        </p>
        <Link
          className="focus-ring mt-8 inline-flex rounded-full bg-teal-700 px-6 py-3 font-bold text-white"
          href="/es/"
        >
          {es["404"].back_home}
        </Link>
      </main>
      <Footer dictionary={es} />
    </>
  );
}
