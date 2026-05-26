import Image from "next/image";
import { site } from "@/content/site";

type WhatsAppFloatingProps = {
  label: string;
};

export function WhatsAppFloating({ label }: WhatsAppFloatingProps) {
  return (
    <a
      aria-label={label}
      className="focus-ring fixed bottom-[88px] right-4 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#25d366] shadow-lg shadow-[#25d366]/30 transition hover:scale-105 hover:shadow-xl hover:shadow-[#25d366]/40 md:bottom-6 md:right-6 md:h-auto md:w-auto md:rounded-full md:px-5 md:py-3"
      href={site.whatsappUrl}
      rel="noopener noreferrer"
      target="_blank"
    >
      <Image
        alt=""
        aria-hidden="true"
        className="h-7 w-7 md:h-5 md:w-5"
        height={28}
        src="/assets/img/WhatsApp.png"
        width={28}
      />
      <span className="ml-2 hidden font-bold text-sm text-white md:inline">{label}</span>
    </a>
  );
}
